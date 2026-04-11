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

namespace vennv\vapm\utils;

use vennv\vapm\FiberManager;
use vennv\vapm\thread\async\Async;

final class PHPUtils implements PHPUtilsInterface {
	public static function forEach(array $array, callable $callback) : Async {
		return new Async(function () use ($array, $callback) {
			foreach ($array as $key => $value) {
				$callback($key, $value);
				FiberManager::wait();
			}
		});
	}

	public static function arrayMap(array $array, callable $callback) : Async {
		return new Async(function () use ($array, $callback) {
			$result = [];
			foreach ($array as $key => $value) {
				$result[$key] = $callback($key, $value);
				FiberManager::wait();
			}
			return $result;
		});
	}

	public static function arrayFilter(array $array, callable $callback) : Async {
		return new Async(function () use ($array, $callback) {
			$result = [];
			foreach ($array as $key => $value) {
				if ($callback($key, $value)) {
					$result[$key] = $value;
				}
				FiberManager::wait();
			}
			return $result;
		});
	}

	public static function arrayReduce(array $array, callable $callback, mixed $initialValue) : Async {
		return new Async(function () use ($array, $callback, $initialValue) {
			$accumulator = $initialValue;
			foreach ($array as $key => $value) {
				$accumulator = $callback($accumulator, $value, $key);
				FiberManager::wait();
			}
			return $accumulator;
		});
	}

	public static function instanceOfAll(array $array, string $className) : Async {
		return new Async(function () use ($array, $className) {
			foreach ($array as $value) {
				if (!($value instanceof $className)) {
					return false;
				}
				FiberManager::wait();
			}
			return true;
		});
	}

	public static function instanceOfAny(array $array, string $className) : Async {
		return new Async(function () use ($array, $className) {
			foreach ($array as $value) {
				if ($value instanceof $className) {
					return true;
				}
				FiberManager::wait();
			}
			return false;
		});
	}
}
