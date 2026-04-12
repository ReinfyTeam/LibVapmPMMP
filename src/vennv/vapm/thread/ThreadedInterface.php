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

namespace vennv\vapm\thread;

interface ThreadedInterface {
	public function getInput() : mixed;

	/**
	 * This method use to get the pid of the thread
	 */
	public function getPid() : int;

	/**
	 * This method use to get the exit code of the thread
	 */
	public function getExitCode() : int;

	/*
	 * This method use to get the running status of the thread
	 */
	public function isRunning() : bool;

	/**
	 * This method use to get the signaled status of the thread
	 */
	public function isSignaled() : bool;

	/**
	 * This method use to get the stopped status of the thread
	 */
	public function isStopped() : bool;

	/**
	 * @return array<string, mixed>
	 * @phpstan-return array<string, mixed>
	 *
	 * This method use to get the shared data of the main thread
	 */
	public static function getDataMainThread() : array;

	/**
	 * @param array<string, mixed> $shared
	 * @phpstan-param array<string, mixed> $shared
	 *
	 * This method use to set the shared data of the main thread
	 */
	public static function setShared(array $shared) : void;

	/**
	 * @phpstan-param mixed $value
	 *
	 * This method use to add the shared data of the MAIN-THREAD
	 */
	public static function addShared(string $key, mixed $value) : void;

	/**
	 * @return array<string, mixed>
	 *
	 * This method use to get the shared data of the child thread
	 */
	public static function getSharedData() : array;

	/**
	 * @param array<string, mixed> $data
	 * @phpstan-param array<string, mixed> $data
	 *
	 * This method use to post all data the main thread
	 */
	public static function postMainThread(array $data) : void;

	/**
	 * @return void
	 *
	 * This method use to load the shared data from the main thread
	 */
	public static function loadSharedData(string $data) : void;

	/**
	 * @return void
	 *
	 * This method use to post the data on the thread
	 */
	public static function post(string $data) : void;

	/**
	 * @return void
	 *
	 * This method use to post serialized data on the thread
	 */
	public static function postSerialized(mixed $data) : void;

	/**
	 * @return bool
	 *
	 * This method use to check the thread is running or not
	 */
	public static function threadIsRunning(int $pid) : bool;

	/**
	 * @return bool
	 *
	 * This method use to kill the thread
	 */
	public static function killThread(int $pid) : bool;
}
