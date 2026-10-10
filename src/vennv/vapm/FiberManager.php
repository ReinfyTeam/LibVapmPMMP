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

use Fiber;
use Throwable;
use function is_null;

interface FiberManagerInterface {
	/**
	 * @throws Throwable
	 *
	 * This is a function that waits for the current fiber to finish.
	 */
	public static function wait() : void;
}

final class FiberManager implements FiberManagerInterface {
	/**
	 * @throws Throwable
	 */
	public static function wait() : void {
		if (!is_null(Fiber::getCurrent())) {
			Fiber::suspend();
		}
	}
}