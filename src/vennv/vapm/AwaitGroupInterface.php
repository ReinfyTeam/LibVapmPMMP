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

interface AwaitGroupInterface {
	/**
	 * @return void
	 *
	 * This function is used to add the count to the group
	 */
	public function add(int $count) : void;

	/**
	 * @return Generator
	 *
	 * This function is used to decrement the count
	 */
	public function done() : Generator;

	/**
	 * @return bool
	 *
	 * This function is used to check if the count is zero
	 */
	public function isDone() : bool;

	/**
	 * @return int
	 *
	 * This function is used to get the count
	 */
	public function getCount() : int;

	/**
	 * @return void
	 *
	 * This function is used to reset the count
	 */
	public function reset() : void;

	/**
	 * @return void
	 *
	 * This function is used to wait for the count to be zero
	 */
	public function wait() : void;
}
