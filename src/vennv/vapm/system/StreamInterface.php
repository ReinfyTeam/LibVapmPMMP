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

interface StreamInterface {
	/**
	 * @throws Throwable
	 *
	 * Use this to read a file or url.
	 */
	public static function read(string $path) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to write to a file.
	 */
	public static function write(string $path, string $data) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to append to a file.
	 */
	public static function append(string $path, string $data) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to delete a file.
	 */
	public static function delete(string $path) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to create a file.
	 */
	public static function create(string $path) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to create a file or overwrite a file.
	 */
	public static function overWrite(string $path, string $data) : Promise;

	/**
	 * @param array<int|string, mixed> $array
	 * @throws Throwable
	 *
	 * Use this to flatten an array.
	 */
	public static function flattenArray(array $array) : Promise;
}
