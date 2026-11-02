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
use function count;

interface DeferredInterface {
	/**
	 * This method is used to get the result of the deferred.
	 */
	public function await() : Generator;

	/**
	 * @return Generator
	 *
	 * This method is used to get all the results of the deferred.
	 */
	public static function awaitAll(DeferredInterface ...$deferreds) : Generator;

	/**
	 * @return Generator
	 *
	 * This method is used to get the first result of the deferred.
	 */
	public static function awaitAny(DeferredInterface ...$deferreds) : Generator;

	/**
	 * This method is used to check if the deferred is finished.
	 */
	public function isFinished() : bool;

	/**
	 * This method is used to get the child coroutine of the deferred.
	 */
	public function getChildCoroutine() : ChildCoroutine;

	/**
	 * This method is used to get the result of the deferred.
	 */
	public function getComplete() : mixed;
}
