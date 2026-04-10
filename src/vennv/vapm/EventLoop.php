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

use Generator;
use SplQueue;
use Throwable;
use function array_keys;
use function count;
use function intdiv;
use function max;
use function min;
use const PHP_INT_MAX;

interface EventLoopInterface {
	public static function init() : void;

	public static function generateId() : int;

	public static function addQueue(Promise $promise) : void;

	public static function getQueue(int $id) : ?Promise;

	public static function addReturn(Promise $promise) : void;

	public static function removeReturn(int $id) : void;

	public static function isReturn(int $id) : bool;

	public static function getReturn(int $id) : ?Promise;

	/**
	 * @return Generator<int, Promise>
	 */
	public static function getReturns() : Generator;
}

class EventLoop implements EventLoopInterface {
	protected const LIMIT = 256; // minimum promises processed per run

	protected const MAX_LIMIT = 8192; // upper bound per run to protect low-end CPUs

	private const GC_SWEEP_SIZE = 2048;

	private const GC_SWEEP_INTERVAL = 8;

	protected static int $nextId = 0;

	/** @var SplQueue<Promise> */
	protected static SplQueue $queues;

	/** @var array<int, Promise> */
	protected static array $returns = [];

	/** @var array<int, int> */
	private static array $gcKeys = [];

	private static int $gcCursor = 0;

	private static int $runCounter = 0;

	private static bool $gcDirty = true;

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
		self::$queues->enqueue($promise);
	}

	public static function getQueue(int $id) : ?Promise {
		while (!self::$queues->isEmpty()) {
			/** @var Promise $promise */
			$promise = self::$queues->dequeue();
			if ($promise->getId() === $id) {
				return $promise;
			}
			self::$queues->enqueue($promise);
		}
		return null;
	}

	public static function addReturn(Promise $promise) : void {
		self::$returns[$promise->getId()] = $promise;
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

		$checks = min(self::GC_SWEEP_SIZE, $remaining);
		for ($i = 0; $i < $checks; $i++) {
			$id = self::$gcKeys[self::$gcCursor++];
			$promise = self::$returns[$id] ?? null;
			if ($promise instanceof Promise && $promise->canDrop()) {
				unset(self::$returns[$id]);
				self::$gcDirty = true;
			}
		}
	}

	private static function calculateBudget(int $queueCount) : int {
		if ($queueCount <= 0) {
			return 0;
		}
		$scaled = intdiv($queueCount, 64);
		return max(self::LIMIT, min(self::MAX_LIMIT, self::LIMIT + $scaled));
	}

	/**
	 * @throws Throwable
	 */
	protected static function run() : void {
		CoroutineGen::run();

		$budget = self::calculateBudget(self::$queues->count());
		$i = 0;
		while (!self::$queues->isEmpty() && $i++ < $budget) {
			/** @var Promise $promise */
			$promise = self::$queues->dequeue();
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
				self::$queues->enqueue($promise);
			}
		}

		MicroTask::isPrepare() && MicroTask::run();
		MacroTask::isPrepare() && MacroTask::run();

		if (++self::$runCounter % self::GC_SWEEP_INTERVAL === 0) {
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
