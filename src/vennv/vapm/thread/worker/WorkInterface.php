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

namespace vennv\vapm\thread\worker;

use Generator;
use vennv\vapm\thread\ClosureThread;

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
	 * @return array<int, ClosureThread>
	 * @phpstan-return array<int, ClosureThread>
	 */
	public function dequeueChunk(int $size) : array;

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
