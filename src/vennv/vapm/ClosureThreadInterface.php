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
use function call_user_func;
use function is_array;
use function is_bool;
use function is_callable;
use function is_null;
use function is_object;
use function is_scalar;
use function iterator_to_array;
use function json_encode;

interface ClosureThreadInterface {
	/**
	 * @return void
	 *
	 * This function runs the callback function for the thread.
	 */
	public function onRun() : void;
}
