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