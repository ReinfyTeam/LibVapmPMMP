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

use Closure;
use Generator;
use ReflectionException;
use SplQueue;
use Throwable;
use function is_callable;

interface CoroutineGenInterface {
	/**
	 * @return SplQueue<ChildCoroutine>|null
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
