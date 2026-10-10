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

interface GeneratorManagerInterface {
	/**
	 * @return int
	 *
	 * This is a function that calculates the seconds from milliseconds for Generator vapm.
	 * For example, if you run a function with multiple yields, this calculates the time spent on each of them in seconds.
	 */
	public static function calculateSeconds(int $milliseconds) : int;
}

final class GeneratorManager implements GeneratorManagerInterface {
	public static function calculateSeconds(int $milliseconds) : int {
		return $milliseconds * 2000;
	}
}
