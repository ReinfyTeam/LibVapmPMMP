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

namespace vennv\vapm\coroutine;

use Exception;
use Generator;

final class ChildCoroutine implements ChildCoroutineInterface {
	protected Generator $coroutine;

	protected Exception $exception;

	public function __construct(Generator $coroutine) {
		$this->coroutine = $coroutine;
	}

	public function setException(Exception $exception) : void {
		$this->exception = $exception;
	}

	public function run() : ChildCoroutine {
		$this->coroutine->send($this->coroutine->current());
		return $this;
	}

	public function isFinished() : bool {
		return !$this->coroutine->valid();
	}

	public function getReturn() : mixed {
		return $this->coroutine->getReturn();
	}
}
