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

use Throwable;
use vennv\vapm\promise\Promise;

interface SystemInterface {
	/**
	 * @throws Throwable
	 *
	 * This function is used to run the event loop with multiple event loops
	 */
	public static function runEventLoop() : void;

	/**
	 * @throws Throwable
	 *
	 * This function is used to run the event loop with single event loop
	 */
	public static function runSingleEventLoop() : void;

	/**
	 * @throws Throwable
	 *
	 * This function is used to initialize the event loop
	 */
	public static function init() : void;

	/**
	 * This function is used to run a callback in the event loop with timeout
	 */
	public static function setTimeout(callable $callback, int $timeout) : SampleMacro;

	/**
	 * This function is used to clear the timeout
	 */
	public static function clearTimeout(SampleMacro $sampleMacro) : void;

	/**
	 * This function is used to run a callback in the event loop with interval
	 */
	public static function setInterval(callable $callback, int $interval) : SampleMacro;

	/**
	 * This function is used to clear the interval
	 */
	public static function clearInterval(SampleMacro $sampleMacro) : void;

	/**
	 * @param array<string|null, string|array> $options
	 * @return Promise when Promise resolve InternetRequestResult and when Promise reject Error
	 * @throws Throwable
	 * @phpstan-param array{method?: string, headers?: array<int, string>, timeout?: int, body?: array<string, string>} $options
	 */
	public static function fetch(string $url, array $options = []) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to curl multiple addresses at once
	 */
	public static function fetchAll(string ...$curls) : Promise;

	/**
	 * @throws Throwable
	 *
	 * This is a function used only to retrieve results from an address or file path via the file_get_contents method
	 */
	public static function read(string $path) : Promise;

	/**
	 * @return void
	 *
	 * This function is used to start a timer
	 */
	public static function time(string $name = 'Console') : void;

	/**
	 * @return void
	 *
	 * This function is used to end a timer
	 */
	public static function timeEnd(string $name = 'Console') : void;
}
