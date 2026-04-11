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

namespace vennv\vapm\thread\worker;

use Throwable;
use vennv\vapm\thread\async\Async;

/**
 * Class Worker
 * @package vennv\vapm
 *
 * This class is used to create a worker to run the work.
 * All asynchronous methods are based on this class.
 */
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
