<?php

/*
 *
 *  ____           _            __           _____
 * |  _ \    ___  (_)  _ __    / _|  _   _  |_   _|   ___    __ _   _ __ ___
 * | |_) |  / _ \ | | | '_ \  | |_  | | | |   | |    / _ \  / _` | | '_ ` _ \
 * |  _ <  |  __/ | | | | | | |  _| | |_| |   | |   |  __/ | (_| | | | | | | |
 * |_| \_\  \___| |_| |_| |_| |_|    \__, |   |_|    \___|  \__,_| |_| |_| |_|
 *                                   |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zuri attempts to enforce "vanilla Minecraft" mechanics, as well as preventing
 * players from abusing weaknesses in Minecraft or its protocol, making your server
 * more safe. Organized in different sections, various checks are performed to test
 * players doing, covering a wide range including flying and speeding, fighting
 * hacks, fast block breaking and nukers, inventory hacks, chat spam and other types
 * of malicious behaviour.
 *
 * @author ReinfyTeam
 * @link https://github.com/ReinfyTeam/
 *
 *
 */

declare(strict_types=1);

namespace vennv\vapm\system;

use Throwable;
use vennv\vapm\coroutine\CoroutineGen;
use vennv\vapm\FiberManager;
use vennv\vapm\network\Internet;
use vennv\vapm\promise\Promise;
use vennv\vapm\system\event\EventLoop;
use vennv\vapm\task\MacroTask;
use vennv\vapm\utils\exceptions\Error;
use function array_values;
use function curl_init;
use function curl_multi_add_handle;
use function curl_multi_close;
use function curl_multi_exec;
use function curl_multi_getcontent;
use function curl_multi_init;
use function curl_multi_remove_handle;
use function curl_setopt;
use function file_get_contents;
use function function_exists;
use function microtime;
use function register_shutdown_function;
use function register_tick_function;
use function trigger_error;
use const CURLM_OK;
use const CURLOPT_RETURNTRANSFER;
use const E_USER_WARNING;

final class System extends EventLoop implements SystemInterface {
	/** @var array<string, int|float> */
	private static array $timings = [];

	private static bool $hasInit = false;

	private static bool $pmmpManaged = false;

	private static bool $shutdownRegistered = false;

	private static bool $tickRegistered = false;

	private static int $eventLoopRuns = 0;

	private static int $lastDroppedReturns = 0;

	public static function setPmmpManaged(bool $managed) : void {
		self::$pmmpManaged = $managed;
	}

	/**
	 * @throws Throwable
	 */
	public static function runEventLoop() : void {
		self::init();
		parent::run();
		self::afterRun();
	}

	/**
	 * @throws Throwable
	 */
	public static function runSingleEventLoop() : void {
		self::init();
		parent::runSingle();
	}

	public static function init() : void {
		if (!self::$hasInit) {
			self::$hasInit = true;
			if (!self::$pmmpManaged) {
				self::registerRuntimeHooks();
			}
		}

		parent::init();
	}

	private static function registerRuntimeHooks() : void {
		if (!function_exists("register_shutdown_function") || !function_exists("register_tick_function")) {
			trigger_error(Error::SYSTEM_HOOKS_UNAVAILABLE, E_USER_WARNING);
			return;
		}

		if (!self::$shutdownRegistered) {
			self::$shutdownRegistered = true;
			register_shutdown_function(fn() => self::runSingleEventLoop());
		}

		if (!self::$tickRegistered) {
			self::$tickRegistered = true;
			register_tick_function(fn() => CoroutineGen::run());
		}
	}

	private static function afterRun() : void {
		self::$eventLoopRuns++;
		$snapshot = EventLoop::getMetricsSnapshot();

		$debugInterval = Settings::getDebugLogIntervalTicks();
		if (Settings::isSchedulerDebugEnabled() && self::$eventLoopRuns % $debugInterval === 0) {
			echo "[LibVapmPMMP] tick=" . self::$eventLoopRuns .
				" queue=" . $snapshot["queueDepth"] .
				" coroutine=" . $snapshot["coroutineBacklog"] .
				" micro=" . $snapshot["microTaskBacklog"] .
				" macro=" . $snapshot["macroTaskBacklog"] .
				" drops=" . $snapshot["droppedReturns"] .
				"\n";
		}

		$warnInterval = Settings::getHealthWarnIntervalTicks();
		if ($warnInterval > 0 && self::$eventLoopRuns % $warnInterval === 0) {
			$currentDrops = $snapshot["droppedReturns"];
			$newDrops = $currentDrops - self::$lastDroppedReturns;
			self::$lastDroppedReturns = $currentDrops;

			if ($snapshot["totalBacklog"] >= Settings::getHealthWarnBacklogThreshold() || $newDrops >= Settings::getHealthWarnDropThreshold()) {
				trigger_error(
					"[LibVapmPMMP] scheduler health warning: totalBacklog=" . $snapshot["totalBacklog"] .
					", queueDepth=" . $snapshot["queueDepth"] .
					", droppedReturnsDelta=" . $newDrops,
					E_USER_WARNING
				);
			}
		}
	}

	/**
	 * @throws Throwable
	 */
	public static function setTimeout(callable $callback, int $timeout) : SampleMacro {
		self::init();
		$sampleMacro = new SampleMacro($callback, $timeout);
		MacroTask::addTask($sampleMacro);
		return $sampleMacro;
	}

	public static function clearTimeout(SampleMacro $sampleMacro) : void {
		if ($sampleMacro->isRunning() && !$sampleMacro->isRepeat()) {
			$sampleMacro->stop();
		}
	}

	/**
	 * @throws Throwable
	 */
	public static function setInterval(callable $callback, int $interval) : SampleMacro {
		self::init();
		$sampleMacro = new SampleMacro($callback, $interval, true);
		MacroTask::addTask($sampleMacro);
		return $sampleMacro;
	}

	public static function clearInterval(SampleMacro $sampleMacro) : void {
		if ($sampleMacro->isRunning() && $sampleMacro->isRepeat()) {
			$sampleMacro->stop();
		}
	}

	/**
	 * @param array<string|null, string|array> $options
	 * @return Promise when Promise resolve InternetRequestResult and when Promise reject Error
	 * @throws Throwable
	 * @phpstan-param array{method?: string, headers?: array<int, string>, timeout?: int, body?: array<string, string>} $options
	 */
	public static function fetch(string $url, array $options = []) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($url, $options) {
			self::setTimeout(function () use ($resolve, $reject, $url, $options) {
				$method = $options["method"] ?? "GET";

				/** @var array<int, string> $headers */
				$headers = $options["headers"] ?? [];
				/** @var list<string> $headers */
				$headers = array_values($headers);

				/** @var int $timeout */
				$timeout = $options["timeout"] ?? 10;

				/** @var array<string, string> $body */
				$body = $options["body"] ?? [];

				$method === "GET" ? $result = Internet::getURL($url, $timeout, $headers) : $result = Internet::postURL($url, $body, $timeout, $headers);
				$result === null ? $reject(Error::FAILED_IN_FETCHING_DATA) : $resolve($result);
			}, 0);
		});
	}

	/**
	 * @throws Throwable
	 *
	 * Use this to curl multiple addresses at once
	 */
	public static function fetchAll(string ...$curls) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($curls) : void {
			$multiHandle = curl_multi_init();
			$handles = [];
			foreach ($curls as $url) {
				$handle = curl_init($url);
				if ($handle === false) {
					$reject(Error::FAILED_IN_FETCHING_DATA);
				} else {
					curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
					curl_multi_add_handle($multiHandle, $handle);
					$handles[] = $handle;
				}
			}

			$running = 0;

			do {
				$status = curl_multi_exec($multiHandle, $running);
				if ($status !== CURLM_OK) {
					$reject(Error::FAILED_IN_FETCHING_DATA);
				}
				FiberManager::wait();
			} while ($running > 0);

			$results = [];
			foreach ($handles as $handle) {
				$results[] = curl_multi_getcontent($handle);
				curl_multi_remove_handle($multiHandle, $handle);
			}

			curl_multi_close($multiHandle);
			$resolve($results);
		});
	}

	/**
	 * @throws Throwable
	 */
	public static function read(string $path) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($path) {
			self::setTimeout(function () use ($resolve, $reject, $path) {
				$ch = file_get_contents($path);
				$ch === false ? $reject(Error::FAILED_IN_FETCHING_DATA) : $resolve($ch);
			}, 0);
		});
	}

	public static function time(string $name = 'Console') : void {
		self::$timings[$name] = microtime(true);
	}

	public static function timeEnd(string $name = 'Console') : void {
		if (!isset(self::$timings[$name])) {
			return;
		}
		$time = microtime(true) - self::$timings[$name];
		echo "Time for $name: $time\n";
		unset(self::$timings[$name]);
	}
}
