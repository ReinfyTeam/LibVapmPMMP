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


final class CoroutineGen implements CoroutineGenInterface {
	/** @var SplQueue<ChildCoroutine>|null */
	private static ?SplQueue $taskQueue = null;

	/** @return SplQueue<ChildCoroutine>|null */
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

	/**
	 * @throws ReflectionException
	 * @throws Throwable
	 */
	public static function run() : void {
		if (!self::$taskQueue?->isEmpty()) {
			$coroutine = self::$taskQueue?->dequeue();
			if ($coroutine instanceof ChildCoroutine && !$coroutine->isFinished()) {
				self::schedule($coroutine->run());
			}
		}
	}
}
