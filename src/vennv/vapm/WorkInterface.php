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
use function array_values;

interface WorkInterface {
	/**
	 * @param array<int, mixed> $args
	 * @return void
	 *
	 * The work is a function that will be executed when the work is run.
	 */
	public function add(callable $work, array $args = []) : void;

	/**
	 * @return void
	 *
	 * Remove the work from the work list.
	 */
	public function remove(int $index) : void;

	/**
	 * @return void
	 *
	 * Remove all works from the work list.
	 */
	public function clear() : void;

	/**
	 * @return int
	 *
	 * Get the number of works in the work list.
	 */
	public function count() : int;

	/**
	 * @return bool
	 *
	 * Check if the work list is empty.
	 */
	public function isEmpty() : bool;

	/**
	 * @return mixed
	 *
	 * Get the first work in the work list.
	 */
	public function dequeue() : mixed;

	/**
	 * @return Generator
	 *
	 * Get the work list by number.
	 */
	public function getArrayByNumber(int $number) : Generator;

	/**
	 * @return Generator
	 *
	 * Get all works in the work list.
	 */
	public function getAll() : Generator;

	/**
	 * @return void
	 *
	 * Run all works in the work list.
	 */
	public function run() : void;
}
