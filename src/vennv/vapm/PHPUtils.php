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

use Throwable;
use function array_keys;
use function array_merge;
use function class_implements;
use function class_parents;
use function get_class;
use function is_object;
use function ltrim;
use function strtolower;

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
				if (!self::isInstanceOf($value, $className)) {
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
				if (self::isInstanceOf($value, $className)) {
					return true;
				}
				FiberManager::wait();
			}
			return false;
		});
	}

	private static function isInstanceOf(mixed $value, string $className) : bool {
		if (!is_object($value)) {
			return false;
		}

		$target = strtolower(ltrim($className, '\\'));
		$candidates = [get_class($value)];
		$candidates = array_merge($candidates, array_keys(class_parents($value) ?: []), array_keys(class_implements($value) ?: []));

		foreach ($candidates as $candidate) {
			if (strtolower(ltrim($candidate, '\\')) === $target) {
				return true;
			}
		}

		return false;
	}
}
