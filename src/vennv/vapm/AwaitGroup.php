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

namespace vennv\vapm;

use Generator;

/**
 * @author  VennDev <venn.dev@gmail.com>
 * @package vennv\vapm
 *
 * This interface is used to create a await group object that can be used to wait for a group of coroutines to complete.
 */
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

final class AwaitGroup implements AwaitGroupInterface {
	private int $count = 0;

	public function add(int $count) : void {
		$this->count += $count;
	}

	public function done() : Generator {
		$this->count--;
		yield;
	}

	public function isDone() : bool {
		return $this->count === 0;
	}

	public function getCount() : int {
		return $this->count;
	}

	public function reset() : void {
		$this->count = 0;
	}

	public function wait() : void {
		$gc = new GarbageCollection();
		while ($this->count > 0) {
			CoroutineGen::run();
			$gc->collectWL();
		}
	}

	public function __destruct() {
		$this->reset();
	}
}