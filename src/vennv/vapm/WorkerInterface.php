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

use Throwable;
use function array_shift;
use function call_user_func;
use function count;

interface WorkerInterface {
	/**
	 * @return bool
	 *
	 * Check the worker is started.
	 */
	public function isStarted() : bool;

	/**
	 * @return Work
	 *
	 * Get the work.
	 */
	public function getWork() : Work;

	/**
	 * @return void
	 *
	 * This is method help you to remove the worker from the worker list.
	 * You should call this method when the work is done to avoid memory leaks.
	 */
	public function done() : void;

	/**
	 * @return void
	 *
	 * Collect the result of the work.
	 */
	public function collect(mixed $result) : void;

	/**
	 * @return array<int|string, mixed>
	 *
	 * Get the result of the work.
	 */
	public function get() : array;

	/**
	 * @return bool
	 *
	 * Check the worker is locked.
	 */
	public function isLocked() : bool;

	/**
	 * @return void
	 *
	 * Lock the worker.
	 */
	public function lock() : void;

	/**
	 * @return void
	 *
	 * Unlock the worker.
	 */
	public function unlock() : void;

	/**
	 * @return void
	 *
	 * Add a child worker to the parent worker.
	 */
	public function addWorker(Worker $worker, callable $callback) : void;

	/**
	 * @return Async
	 *
	 * Run the work.
	 * @throws Throwable
	 */
	public function run(callable $callback) : Async;
}
