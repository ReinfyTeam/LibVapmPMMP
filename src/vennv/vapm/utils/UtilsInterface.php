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

namespace vennv\vapm\utils;

use Closure;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;
use ReflectionFunction;
use RuntimeException;
use SplFileInfo;
use vennv\vapm\Error;
use function array_keys;
use function array_slice;
use function array_values;
use function count;
use function debug_backtrace;
use function explode;
use function file;
use function get_resource_type;
use function gettype;
use function implode;
use function intdiv;
use function is_array;
use function is_bool;
use function is_callable;
use function is_null;
use function is_object;
use function is_resource;
use function is_string;
use function json_decode;
use function json_encode;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function serialize;
use function str_replace;
use function strlen;
use function strpos;
use function strrpos;
use function substr;

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
	 * @return string
	 *
	 * Transform a string to inline
	 */
	public static function outlineToInline(string $text) : string;

	/**
	 * @return string
	 *
	 * Fix input command
	 */
	public static function fixInputCommand(string $text) : string;

	/**
	 * @return null|string
	 *
	 * Remove comments from a string
	 */
	public static function removeComments(string $text) : ?string;

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
	 * @return string|null
	 *
	 * Replace advanced
	 */
	public static function replaceAdvanced(string $text, string $search, string $replace) : ?string;

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
