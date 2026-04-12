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
