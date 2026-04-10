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
use function count;
use function intdiv;
use function max;
use function min;
use const PHP_INT_MAX;

final class MacroTask {
	private const BASE_LIMIT = 128;

	private const MAX_LIMIT = 2048;


	private static int $nextId = 0;

	/** @var array<int, SampleMacro> */
	private static array $tasks = [];

	public static function generateId() : int {
		if (self::$nextId >= PHP_INT_MAX) {
			self::$nextId = 0;
		}
		return self::$nextId++;
	}

	public static function addTask(SampleMacro $sampleMacro) : void {
		self::$tasks[$sampleMacro->getId()] = $sampleMacro;
	}

	public static function removeTask(SampleMacro $sampleMacro) : void {
		$id = $sampleMacro->getId();
		if (isset(self::$tasks[$id])) {
			unset(self::$tasks[$id]);
		}
	}

	public static function getTask(int $id) : ?SampleMacro {
		return self::$tasks[$id] ?? null;
	}

	public static function getTasks() : Generator {
		foreach (self::$tasks as $id => $task) {
			yield $id => $task;
		}
	}

	public static function isPrepare() : bool {
		return !empty(self::$tasks);
	}

	public static function run() : void {
		if (self::$tasks === []) {
			return;
		}

		$limit = min(
			self::MAX_LIMIT,
			max(self::BASE_LIMIT, self::BASE_LIMIT + intdiv(count(self::$tasks), 64))
		);

		$gc = new GarbageCollection();
		$processed = 0;
		foreach (self::$tasks as $id => $task) {
			if ($processed++ >= $limit) {
				break;
			}
			/** @var SampleMacro $task */
			if ($task->checkTimeOut()) {
				$task->run();
				if (!$task->isRepeat()) {
					unset(self::$tasks[$id]);
				} else {
					$task->resetTimeOut();
				}
			}
		}
		$gc->collectWL();
	}
}
