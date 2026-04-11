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

namespace vennv\vapm\promise;

use Fiber;
use Throwable;
use vennv\vapm\thread\async\Async;

/**
 * @author  VennDev <venn.dev@gmail.com>
 * @package vennv\vapm
 *
 * This interface is used to create a promise object that can be used to get the result of a coroutine.
 */
interface PromiseInterface {
	/**
	 * @return Promise
	 *
	 * This method is used to set the result of the promise.
	 */
	public function setResult(mixed $result) : Promise;

	/**
	 * @throws Throwable
	 *
	 * This method is used to create a new promise.
	 */
	public static function c(callable $callback, bool $justGetResult = false) : Promise;

	/**
	 * This method is used to get the id of the promise.
	 */
	public function getId() : int;

	/**
	 * This method is used to get the fiber of the promise.
	 */
	public function getFiber() : Fiber;

	/**
	 * This method is used to check if the promise is just to get the result.
	 */
	public function isJustGetResult() : bool;

	/**
	 * This method is used to get the time out of the promise.
	 */
	public function getTimeOut() : float;

	/**
	 * This method is used to get the time start of the promise.
	 */
	public function getTimeStart() : float;

	/**
	 * This method is used to get the time end of the promise.
	 */
	public function getTimeEnd() : float;

	/**
	 * This method is used to set the time out of the promise.
	 */
	public function setTimeEnd(float $timeEnd) : void;

	/**
	 * This method is used to check if the promise is timed out and can be dropped.
	 */
	public function canDrop() : bool;

	/**
	 * This method is used to get the status of the promise.
	 */
	public function getStatus() : string;

	/**
	 * This method is used to check if the promise is pending.
	 */
	public function isPending() : bool;

	/**
	 * This method is used to check if the promise is resolved.
	 */
	public function isResolved() : bool;

	/**
	 * This method is used to check if the promise is rejected.
	 */
	public function isRejected() : bool;

	/**
	 * This method is used to get the result of the promise.
	 */
	public function getResult() : mixed;

	/**
	 * This method is used to get the return when catch or then of the promise is resolved or rejected.
	 */
	public function getReturn() : mixed;

	/**
	 * @throws Throwable
	 *
	 * This method is used to get the callback of the promise.
	 */
	public function getCallback() : callable;

	/**
	 * This method is used to resolve the promise.
	 */
	public function resolve(mixed $value = '') : void;

	/**
	 * This method is used to reject the promise.
	 */
	public function reject(mixed $value = '') : void;

	/**
	 * This method is used to set the callback when the promise is resolved.
	 */
	public function then(callable $callback) : Promise;

	/**
	 * This method is used to set the callback when the promise is rejected.
	 */
	public function catch(callable $callback) : Promise;

	/**
	 * This method is used to set the callback when the promise is resolved or rejected.
	 */
	public function finally(callable $callback) : Promise;

	/**
	 * @throws Throwable
	 *
	 * This method is used to use the callbacks of the promise.
	 */
	public function useCallbacks() : void;

	/**
	 * @param array<int, Async|Promise|callable> $promises
	 * @phpstan-param array<int, Async|Promise|callable> $promises
	 * @throws Throwable
	 */
	public static function all(array $promises) : Promise;

	/**
	 * @param array<int, Async|Promise|callable> $promises
	 * @phpstan-param array<int, Async|Promise|callable> $promises
	 * @throws Throwable
	 */
	public static function allSettled(array $promises) : Promise;

	/**
	 * @param array<int, Async|Promise|callable> $promises
	 * @phpstan-param array<int, Async|Promise|callable> $promises
	 * @throws Throwable
	 */
	public static function any(array $promises) : Promise;

	/**
	 * @param array<int, Async|Promise|callable> $promises
	 * @phpstan-param array<int, Async|Promise|callable> $promises
	 * @throws Throwable
	 */
	public static function race(array $promises) : Promise;
}
