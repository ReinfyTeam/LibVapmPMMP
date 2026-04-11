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

namespace vennv\vapm\system;

use vennv\vapm\task\MacroTask;
use vennv\vapm\utils\Utils;
use function call_user_func;
use function microtime;

final class SampleMacro implements SampleMacroInterface {
	private float $timeOut;

	private float $timeStart;

	private bool $isRepeat;

	/** @var callable $callback */
	private mixed $callback;

	private int $id;

	public function __construct(callable $callback, int $timeOut = 0, bool $isRepeat = false) {
		$this->id = MacroTask::generateId();
		$this->timeOut = Utils::milliSecsToSecs($timeOut);
		$this->isRepeat = $isRepeat;
		$this->timeStart = microtime(true);
		$this->callback = $callback;
	}

	public function isRepeat() : bool {
		return $this->isRepeat;
	}

	public function getTimeOut() : float {
		return $this->timeOut;
	}

	public function getTimeStart() : float {
		return $this->timeStart;
	}

	public function getCallback() : callable {
		return $this->callback;
	}

	public function getId() : int {
		return $this->id;
	}

	public function checkTimeOut() : bool {
		return microtime(true) - $this->timeStart >= $this->timeOut;
	}

	public function resetTimeOut() : void {
		$this->timeStart = microtime(true);
	}

	public function isRunning() : bool {
		return MacroTask::getTask($this->id) !== null;
	}

	public function run() : void {
		call_user_func($this->callback);
	}

	public function stop() : void {
		MacroTask::removeTask($this);
	}
}
