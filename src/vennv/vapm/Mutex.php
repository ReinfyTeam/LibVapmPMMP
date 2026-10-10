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