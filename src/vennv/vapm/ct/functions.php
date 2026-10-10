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

namespace vennv\vapm\ct;

use Closure;
use Generator;
use vennv\vapm\AwaitGroup;
use vennv\vapm\Channel;
use vennv\vapm\CoroutineGen;
use vennv\vapm\Deferred;
use vennv\vapm\Mutex;

/**
 * This file is used to create a coroutine with non-blocking mode
 */

/**
 * @return void
 *
 * This function is used to create a coroutine with non-blocking mode
 */
function c(callable ...$callbacks) : void {
	CoroutineGen::runNonBlocking(...$callbacks);
}

/**
 * @return void
 *
 * This function is used to create a coroutine with blocking mode
 */
function cBlock(callable ...$callbacks) : void {
	CoroutineGen::runBlocking(...$callbacks);
}

/**
 * @return Generator
 *
 * This function is used to delay the execution of a coroutine
 */
function cDelay(int $milliseconds) : Generator {
	return CoroutineGen::delay($milliseconds);
}

/**
 * @return Closure
 *
 * This function is used to repeat the execution of a coroutine
 */
function cRepeat(callable $callback, int $times) : Closure {
	return CoroutineGen::repeat($callback, $times);
}

/**
 * @return Channel
 *
 * This function is used to create a channel
 */
function channel() : Channel {
	return new Channel();
}

/**
 * @return AwaitGroup
 *
 * This function is used to create a await group
 */
function awaitGroup() : AwaitGroup {
	return new AwaitGroup();
}

/**
 * @return Mutex
 *
 * This function is used to create a mutex
 */
function mutex() : Mutex {
	return new Mutex();
}

/**
 * @return Deferred
 *
 * This function is used to create a deferred
 */
function deferred(callable $callback) : Deferred {
	return new Deferred($callback);
}