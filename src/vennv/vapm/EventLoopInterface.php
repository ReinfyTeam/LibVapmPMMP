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

use Generator;
use SplQueue;
use Throwable;
use function is_int;
use const PHP_INT_MAX;

interface EventLoopInterface {
	public static function init() : void;

	public static function generateId() : int;

	public static function addQueue(Promise $promise) : void;

	public static function getQueue(int $id) : ?Promise;

	public static function addReturn(Promise $promise) : void;

	public static function removeReturn(int $id) : void;

	public static function isReturn(int $id) : bool;

	public static function getReturn(int $id) : ?Promise;

	public static function getReturns() : Generator;
}
