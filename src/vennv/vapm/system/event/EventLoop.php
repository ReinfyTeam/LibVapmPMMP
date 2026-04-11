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

namespace vennv\vapm\system\event;

use Generator;
use SplQueue;
use Throwable;
use vennv\vapm\coroutine\CoroutineGen;
use vennv\vapm\promise\Promise;
use vennv\vapm\promise\StatusPromise;
use vennv\vapm\system\Settings;
use vennv\vapm\task\MacroTask;
use vennv\vapm\task\MicroTask;
use vennv\vapm\thread\GarbageCollection;
use function array_keys;
use function count;
use function intdiv;
use function max;
use function min;
use const PHP_INT_MAX;

class EventLoop implements EventLoopInterface {
	protected static int $nextId = 0;

	/** @var SplQueue<Promise> */
	protected static SplQueue $queues;

	/** @var array<int, Promise> */
	protected static array $queueIndex = [];

	/** @var array<int, true> */
	protected static array $queuedIds = [];

	/** @var array<int, Promise> */
	protected static array $returns = [];

	/** @var array<int, int> */
	private static array $gcKeys = [];

	private static int $gcCursor = 0;

	private static int $runCounter = 0;

	private static bool $gcDirty = true;

	/** @var array<string, int> */
	private static array $metrics = [
		"ticks" => 0,
		"processedPromises" => 0,
		"processedCoroutines" => 0,
		"processedMicroTasks" => 0,
		"processedMacroTasks" => 0,
		"droppedReturns" => 0,
		"droppedDuplicateQueue" => 0,
	];

	public static function init() : void {
		self::$queues ??= new SplQueue();
	}

	public static function generateId() : int {
		if (self::$nextId >= PHP_INT_MAX) {
			self::$nextId = 0;
		}
		return self::$nextId++;
	}

	public static function addQueue(Promise $promise) : void {
		$id = $promise->getId();
		if (isset(self::$queuedIds[$id])) {
			self::$metrics["droppedDuplicateQueue"]++;
			return;
		}

		self::$queuedIds[$id] = true;
		self::$queueIndex[$id] = $promise;
		self::$queues->enqueue($promise);
	}

	public static function getQueue(int $id) : ?Promise {
		return self::$queueIndex[$id] ?? null;
	}

	public static function addReturn(Promise $promise) : void {
		$id = $promise->getId();
		unset(self::$queueIndex[$id], self::$queuedIds[$id]);
		self::$returns[$id] = $promise;
		self::$gcDirty = true;
	}

	public static function isReturn(int $id) : bool {
		return isset(self::$returns[$id]);
	}

	public static function removeReturn(int $id) : void {
		if (self::isReturn($id)) {
			unset(self::$returns[$id]);
			self::$gcDirty = true;
		}
	}

	public static function getReturn(int $id) : ?Promise {
		return self::$returns[$id] ?? null;
	}

	/**
	 * @return Generator<int, Promise>
	 */
	public static function getReturns() : Generator {
		foreach (self::$returns as $id => $promise) {
			yield $id => $promise;
		}
	}

	/**
	 * @return array<string, int>
	 * @phpstan-return array<string, int>
	 */
	public static function getMetricsSnapshot() : array {
		$snapshot = self::$metrics;
		$snapshot["queueDepth"] = self::$queues->count();
		$snapshot["returnDepth"] = count(self::$returns);
		$snapshot["coroutineBacklog"] = CoroutineGen::countTasks();
		$snapshot["microTaskBacklog"] = MicroTask::countTasks();
		$snapshot["macroTaskBacklog"] = MacroTask::countTasks();
		$snapshot["totalBacklog"] = $snapshot["queueDepth"]
			+ $snapshot["coroutineBacklog"]
			+ $snapshot["microTaskBacklog"]
			+ $snapshot["macroTaskBacklog"];
		return $snapshot;
	}

	private static function dequeueQueue() : ?Promise {
		if (self::$queues->isEmpty()) {
			return null;
		}

		/** @var Promise $promise */
		$promise = self::$queues->dequeue();
		$id = $promise->getId();
		unset(self::$queuedIds[$id], self::$queueIndex[$id]);
		return $promise;
	}

	/**
	 * @throws Throwable
	 */
	private static function clearGarbage() : void {
		if (self::$returns === []) {
			return;
		}

		if (self::$gcDirty || self::$gcCursor >= count(self::$gcKeys)) {
			self::$gcKeys = array_keys(self::$returns);
			self::$gcCursor = 0;
			self::$gcDirty = false;
		}

		$remaining = count(self::$gcKeys) - self::$gcCursor;
		if ($remaining <= 0) {
			return;
		}

		$checks = min(Settings::getGcSweepSize(), $remaining);
		for ($i = 0; $i < $checks; $i++) {
			$id = self::$gcKeys[self::$gcCursor++];
			$promise = self::$returns[$id] ?? null;
			if ($promise instanceof Promise && $promise->canDrop()) {
				unset(self::$returns[$id]);
				self::$metrics["droppedReturns"]++;
				self::$gcDirty = true;
			}
		}
	}

	private static function calculateBudget(int $queueCount) : int {
		if ($queueCount <= 0) {
			return 0;
		}
		$scaled = intdiv($queueCount, 64);
		$baseLimit = Settings::getEventLoopBaseLimit();
		$maxLimit = Settings::getEventLoopMaxLimit();
		return max($baseLimit, min($maxLimit, $baseLimit + $scaled));
	}

	private static function calculateShare(int $totalBudget, int $backlog, int $totalBacklog) : int {
		if ($backlog <= 0 || $totalBacklog <= 0 || $totalBudget <= 0) {
			return 0;
		}

		return max(1, intdiv($totalBudget * $backlog, $totalBacklog));
	}

	/**
	 * @throws Throwable
	 */
	private static function runPromises(int $limit) : int {
		if ($limit <= 0 || self::$queues->isEmpty()) {
			return 0;
		}

		$processed = 0;
		while (!self::$queues->isEmpty() && $processed < $limit) {
			$promise = self::dequeueQueue();
			if (!$promise instanceof Promise) {
				break;
			}

			$fiber = $promise->getFiber();
			if ($fiber->isSuspended()) {
				$fiber->resume();
			}

			if (
				$fiber->isTerminated() &&
				($promise->getStatus() !== StatusPromise::PENDING || $promise->isJustGetResult())
			) {
				try {
					$promise->isJustGetResult() && $promise->setResult($fiber->getReturn());
				} catch (Throwable $e) {
					echo $e->getMessage();
				}
				MicroTask::addTask($promise->getId(), $promise);
			} else {
				self::addQueue($promise);
			}

			$processed++;
		}

		return $processed;
	}

	/**
	 * @throws Throwable
	 */
	protected static function run() : void {
		$queueBacklog = self::$queues->count();
		$coroutineBacklog = CoroutineGen::countTasks();
		$microTaskBacklog = MicroTask::countTasks();
		$macroTaskBacklog = MacroTask::countTasks();
		$totalBacklog = $queueBacklog + $coroutineBacklog + $microTaskBacklog + $macroTaskBacklog;
		$budget = self::calculateBudget($totalBacklog);

		$promiseBudget = self::calculateShare($budget, $queueBacklog, $totalBacklog);
		$coroutineBudget = self::calculateShare($budget, $coroutineBacklog, $totalBacklog);
		$microTaskBudget = self::calculateShare($budget, $microTaskBacklog, $totalBacklog);
		$macroTaskBudget = self::calculateShare($budget, $macroTaskBacklog, $totalBacklog);

		$processedPromises = self::runPromises($promiseBudget);
		$processedCoroutines = CoroutineGen::runBatch($coroutineBudget);
		$processedMicroTasks = MicroTask::runBatch($microTaskBudget);
		$processedMacroTasks = MacroTask::runBatch($macroTaskBudget);

		self::$metrics["ticks"]++;
		self::$metrics["processedPromises"] += $processedPromises;
		self::$metrics["processedCoroutines"] += $processedCoroutines;
		self::$metrics["processedMicroTasks"] += $processedMicroTasks;
		self::$metrics["processedMacroTasks"] += $processedMacroTasks;

		if (++self::$runCounter % Settings::getGcSweepInterval() === 0) {
			self::clearGarbage();
		}
	}

	/**
	 * @throws Throwable
	 */
	protected static function runSingle() : void {
		$gc = new GarbageCollection();
		while (
			!self::$queues->isEmpty() ||
			(CoroutineGen::getTaskQueue() !== null && !CoroutineGen::getTaskQueue()->isEmpty()) ||
			MicroTask::isPrepare() || MacroTask::isPrepare()
		) {
			self::run();
			$gc->collectWL();
		}
	}
}
