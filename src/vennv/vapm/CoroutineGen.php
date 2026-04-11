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

namespace vennv\vapm;

use Closure;
use Generator;
use ReflectionException;
use SplQueue;
use Throwable;
use function intdiv;
use function is_callable;
use function max;
use function min;
use const PHP_INT_MAX;

interface CoroutineGenInterface {
	/**
	 * @return SplQueue|null
	 *
	 * This function returns the task queue.
	 */
	public static function getTaskQueue() : ?SplQueue;

	/**
	 * @return void
	 *
	 * This is a blocking function that runs all the coroutines passed to it.
	 */
	public static function runNonBlocking(mixed ...$coroutines) : void;

	/**
	 * @return void
	 *
	 * This is a blocking function that runs all the coroutines passed to it.
	 */
	public static function runBlocking(mixed ...$coroutines) : void;

	/**
	 * @return Closure
	 *
	 * This is a generator that runs a callback function a specified amount of times.
	 */
	public static function repeat(callable $callback, int $times) : Closure;

	/**
	 * @return Generator
	 *
	 * This is a generator that yields for a specified amount of milliseconds.
	 */
	public static function delay(int $milliseconds) : Generator;

	/**
	 * @return void
	 *
	 * This function runs the task queue.
	 */
	public static function run() : void;
}

final class CoroutineGen implements CoroutineGenInterface {
	private static ?SplQueue $taskQueue = null;

	public static function getTaskQueue() : ?SplQueue {
		return self::$taskQueue;
	}

	/**
	 * @throws Throwable
	 */
	public static function runNonBlocking(mixed ...$coroutines) : void {
		System::init();
		self::$taskQueue ??= new SplQueue();
		foreach ($coroutines as $coroutine) {
			$result = is_callable($coroutine) ? $coroutine() : $coroutine;
			$result instanceof Generator
				? self::schedule(new ChildCoroutine($result))
				: $result;
		}
		self::run();
	}

	/**
	 * @throws Throwable
	 */
	public static function runBlocking(mixed ...$coroutines) : void {
		self::runNonBlocking(...$coroutines);
		$gc = new GarbageCollection();
		while (!self::$taskQueue?->isEmpty()) {
			self::run();
			$gc->collectWL();
		}
	}

	private static function processCoroutine(mixed ...$coroutines) : Closure {
		return function () use ($coroutines) : void {
			foreach ($coroutines as $coroutine) {
				$result = is_callable($coroutine) ? $coroutine() : $coroutine;
				$result instanceof Generator
					? self::schedule(new ChildCoroutine($result))
					: $result;
			}
			self::run();
		};
	}

	public static function repeat(callable $callback, int $times) : Closure {
		$gc = new GarbageCollection();
		for ($i = 0; $i < $times; $i++) {
			$result = $callback();
			if ($result instanceof Generator) {
				$callback = self::processCoroutine($result);
			}
			$gc->collectWL();
		}
		return fn() => null;
	}

	public static function delay(int $milliseconds) : Generator {
		for ($i = 0; $i < GeneratorManager::calculateSeconds($milliseconds); $i++) {
			yield;
		}
	}

	private static function schedule(ChildCoroutine $childCoroutine) : void {
		self::$taskQueue?->enqueue($childCoroutine);
	}

	public static function countTasks() : int {
		return self::$taskQueue?->count() ?? 0;
	}

	/**
	 * @throws ReflectionException
	 * @throws Throwable
	 */
	public static function runBatch(int $limit = PHP_INT_MAX) : int {
		$taskQueue = self::$taskQueue;
		if ($taskQueue === null || $taskQueue->isEmpty() || $limit <= 0) {
			return 0;
		}

		$processed = 0;
		while (!$taskQueue->isEmpty() && $processed < $limit) {
			$coroutine = $taskQueue->dequeue();
			if ($coroutine instanceof ChildCoroutine && !$coroutine->isFinished()) {
				self::schedule($coroutine->run());
			}
			$processed++;
		}

		return $processed;
	}

	/**
	 * @throws ReflectionException
	 * @throws Throwable
	 */
	public static function run() : void {
		$taskQueue = self::$taskQueue;
		if ($taskQueue === null || $taskQueue->isEmpty()) {
			return;
		}

		$limit = min(
			Settings::getCoroutineMaxLimit(),
			max(
				Settings::getCoroutineBaseLimit(),
				Settings::getCoroutineBaseLimit() + intdiv($taskQueue->count(), 32)
			)
		);
		self::runBatch($limit);
	}
}
