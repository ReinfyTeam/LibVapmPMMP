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

namespace vennv\vapm\io;

use vennv\vapm\promise\Promise;
use vennv\vapm\system\System;
use vennv\vapm\thread\async\Async;

final class Io {
	public static function async(callable $callback) : Async {
		return new Async($callback);
	}

	/**
	 * @param mixed $await
	 * @return mixed
	 */
	public static function await($await) {
		return Async::await($await);
	}

	public static function delay(int $milliseconds) : Promise {
		return new Promise(function (callable $resolve) use ($milliseconds) : void {
			System::setTimeout(function () use ($resolve) : void {
				$resolve();
			}, $milliseconds);
		});
	}

	public static function setTimeout(callable $callback, int $milliseconds) : void {
		System::setTimeout($callback, $milliseconds);
	}

	public static function setInterval(callable $callback, int $milliseconds) : void {
		System::setInterval($callback, $milliseconds);
	}
}
