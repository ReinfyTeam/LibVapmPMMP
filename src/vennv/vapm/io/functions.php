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

namespace vennv\vapm\io;

use vennv\vapm\Async;
use vennv\vapm\Promise;
use vennv\vapm\System;

/**
 * This functions is used to handle the IO operations
 */

/**
 * @return Async
 *
 * This function is used to run asynchronous callbacks
 */
function async(callable $callback) : Async {
	return new Async($callback);
}

/**
 * @return mixed
 *
 * This function is used to wait for a callback to be executed
 */
function await(mixed $await) : mixed {
	return Async::await($await);
}

/**
 * @return Promise
 *
 * This function is used to delay the execution of a callback
 */
function delay(int $milliseconds) : Promise {
	return new Promise(function($resolve) use ($milliseconds) {
		System::setTimeout(function() use ($resolve) {
			$resolve();
		}, $milliseconds);
	});
}

/**
 * @return void
 *
 * This function is used to set a timeout for a callback
 */
function setTimeout(callable $callback, int $milliseconds) : void {
	System::setTimeout($callback, $milliseconds);
}

/**
 * @return void
 *
 * This function is used to set an interval for a callback
 */
function setInterval(callable $callback, int $milliseconds) : void {
	System::setInterval($callback, $milliseconds);
}