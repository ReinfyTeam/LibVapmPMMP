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

namespace vennv\vapm\utils;

use Closure;
use Generator;
use ReflectionException;
use RuntimeException;

interface UtilsInterface {
	/**
	 * Transform milliseconds to seconds
	 */
	public static function milliSecsToSecs(float $milliSecs) : float;

	/**
	 * @throws ReflectionException
	 *
	 * Transform a closure or callable to string
	 */
	public static function closureToString(Closure $closure) : string;

	/**
	 * @throws RuntimeException
	 * @return string
	 *
	 * Transform a closure or callable to string
	 */
	public static function closureToStringSafe(Closure $closure) : string;

	/**
	 * Get all Dot files in a directory
	 */
	public static function getAllByDotFile(string $path, string $dotFile) : Generator;

	/**
	 * @return array<int, string>|string
	 *
	 * Transform a string to inline
	 */
	public static function outlineToInline(string $text) : array|string;

	/**
	 * @return array<int, string>|string
	 *
	 * Fix input command
	 */
	public static function fixInputCommand(string $text) : array|string;

	/**
	 * @return null|string|array<int, string>
	 *
	 * Remove comments from a string
	 */
	public static function removeComments(string $text) : null|string|array;

	/**
	 * @param mixed $data
	 *
	 * Get bytes of a string or object or array
	 */
	public static function getBytes(mixed $data) : int;

	/**
	 * @return Generator
	 *
	 * Split a string by slash
	 */
	public static function splitStringBySlash(string $string) : Generator;

	/**
	 * @return false|string
	 *
	 * Replace path
	 */
	public static function replacePath(string $path, string $segment) : false|string;

	/**
	 * @return array<int, string>|string|null
	 *
	 * Replace advanced
	 */
	public static function replaceAdvanced(string $text, string $search, string $replace) : array|string|null;

	/**
	 * @return Generator
	 *
	 * Evenly divide a number
	 */
	public static function evenlyDivide(int $number, int $parts) : Generator;

	/**
	 * @param array<int, mixed> $array
	 */
	public static function splitArray(array $array, int $size) : Generator;

	/**
	 * @return bool
	 *
	 * This method is used to check if the current class is the same as the class passed in
	 */
	public static function isClass(string $class) : bool;

	/**
	 * @return string
	 *
	 * Get string after sign
	 */
	public static function getStringAfterSign(string $string, string $sign) : string;

	/**
	 * @return array<int|string, bool|string>
	 *
	 * Convert data to string
	 */
	public static function toStringAny(mixed $data) : array;

	/**
	 * @param array<string, string> $data
	 * @return mixed
	 *
	 * Convert data to real it's type
	 */
	public static function fromStringToAny(array $data) : mixed;
}
