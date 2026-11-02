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

use vennv\vapm\utils\Utils;
use function call_user_func;
use function microtime;

interface SampleMacroInterface {
	public function isRepeat() : bool;

	public function getTimeOut() : float;

	public function getTimeStart() : float;

	public function getCallback() : callable;

	public function getId() : int;

	public function checkTimeOut() : bool;

	public function resetTimeOut() : void;

	public function isRunning() : bool;

	public function run() : void;

	public function stop() : void;
}
