<?php

/*
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * LibVapmPMMP provides asynchronous, promise, coroutine and thread utilities
 * for PocketMine-MP.
 * Original authors: VennDev, xqwtxon and xwertxy.
 * Maintained by ReinfyTeam.
 *
 * @link https://github.com/ReinfyTeam/LibVapmPMMP
 *
 *
 */

declare(strict_types=1);

namespace vennv\vapm;

use Generator;
use Throwable;
use function microtime;

final class MicroTask {
	/** @var array<int, Promise> */
	private static array $tasks = [];

	public static function addTask(int $id, Promise $promise) : void {
		self::$tasks[$id] = $promise;
	}

	public static function removeTask(int $id) : void {
		unset(self::$tasks[$id]);
	}

	public static function getTask(int $id) : ?Promise {
		return self::$tasks[$id] ?? null;
	}

	public static function getTasks() : Generator {
		foreach (self::$tasks as $id => $promise) {
			yield $id => $promise;
		}
	}

	public static function isPrepare() : bool {
		return !empty(self::$tasks);
	}

	/**
	 * @throws Throwable
	 */
	public static function run() : void {
		$gc = new GarbageCollection();
		foreach (self::getTasks() as $id => $promise) {
			/** @var Promise $promise */
			$promise->useCallbacks();
			$promise->setTimeEnd(microtime(true));
			EventLoop::addReturn($promise);
			/** @var int $id */
			self::removeTask($id);
			$gc->collectWL();
		}
	}
}