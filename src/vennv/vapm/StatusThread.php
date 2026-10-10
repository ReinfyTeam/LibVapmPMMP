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

use function microtime;

interface StatusThreadInterface {
	/**
	 * @return int|float
	 *
	 * This method is used to get the time sleeping.
	 */
	public function getTimeSleeping() : int|float;

	/**
	 * @return float
	 *
	 * This method is used to get the sleep start time.
	 */
	public function getSleepStartTime() : float;

	/**
	 * @param int|float $seconds
	 *
	 * This method is used to sleep the thread.
	 */
	public function sleep(int|float $seconds) : void;

	/**
	 * @return bool
	 *
	 * This method is used to check if the thread can wake up.
	 */
	public function canWakeUp() : bool;
}

final class StatusThread implements StatusThreadInterface {
	private int|float $timeSleeping = 0;

	private float $sleepStartTime;

	public function __construct() {
		$this->sleepStartTime = microtime(true);
	}

	public function getTimeSleeping() : int|float {
		return $this->timeSleeping;
	}

	public function getSleepStartTime() : float {
		return $this->sleepStartTime;
	}

	public function sleep(int|float $seconds) : void {
		$this->timeSleeping += $seconds;
	}

	public function canWakeUp() : bool {
		return microtime(true) - $this->sleepStartTime >= $this->timeSleeping;
	}
}
