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

use Throwable;
use vennv\vapm\thread\async\Async;

interface PHPUtilsInterface {
	/**
	 * @param array<int|float|string|object> $array
	 *
	 * @phpstan-param array<int|float|string|object> $array
	 * @throws Throwable
	 *
	 * This function is used to iterate over an array and call a callback function for each element.
	 */
	public static function forEach(array $array, callable $callback) : Async;

	/**
	 * @param array<int|float|string|object> $array
	 *
	 * @phpstan-param array<int|float|string|object> $array
	 * @throws Throwable
	 *
	 * This function is used to map over an array and apply a callback function to each element.
	 */
	public static function arrayMap(array $array, callable $callback) : Async;

	/**
	 * @param array<int|float|string|object> $array
	 *
	 * @phpstan-param array<int|float|string|object> $array
	 * @throws Throwable
	 */
	public static function arrayFilter(array $array, callable $callback) : Async;

	/**
	 * @param array<int|float|string|object> $array
	 *
	 * @throws Throwable
	 *
	 * This function is used to reduce an array to a single value by applying a callback function to each element.
	 */
	public static function arrayReduce(array $array, callable $callback, mixed $initialValue) : Async;

	/**
	 * @param array<int|float|string|object> $array
	 *
	 * @throws Throwable
	 *
	 * This function is used to check if all elements in an array are instances of a specific class.
	 */
	public static function instanceOfAll(array $array, string $className) : Async;

	/**
	 * @param array<int|float|string|object> $array
	 *
	 * @throws Throwable
	 *
	 * This function is used to check if any element in an array is an instance of a specific class.
	 */
	public static function instanceOfAny(array $array, string $className) : Async;
}
