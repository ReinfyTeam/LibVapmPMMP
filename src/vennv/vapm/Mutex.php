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
 * This class is used to create a mutex object that can be used to synchronize access to shared resources.
 * Note: this just for coroutine, if you want to use it in other places, you need to implement it yourself.
 */
interface MutexInterface {
	/**
	 * @return bool
	 *
	 * This function returns the lock status.
	 */
	public function isLocked() : bool;

	/**
	 * @return Generator
	 *
	 * This function locks the mutex.
	 */
	public function lock() : Generator;

	/**
	 * @return Generator
	 *
	 * This function unlocks the mutex.
	 */
	public function unlock() : Generator;
}

final class Mutex implements MutexInterface {
	private bool $locked = false;

	/**
	 * @return bool
	 *
	 * This function returns the lock status.
	 */
	public function isLocked() : bool {
		return $this->locked;
	}

	/**
	 * @return Generator
	 *
	 * This function locks the mutex.
	 */
	public function lock() : Generator {
		while ($this->locked) {
			yield;
		}
		$this->locked = true;
	}

	/**
	 * @return Generator
	 *
	 * This function unlocks the mutex.
	 */
	public function unlock() : Generator {
		yield $this->locked = false;
	}
}