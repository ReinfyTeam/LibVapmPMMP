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
	return new Promise(function(callable $resolve) use ($milliseconds) : void {
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
