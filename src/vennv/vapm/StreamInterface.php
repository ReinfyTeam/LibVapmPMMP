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
use function array_unshift;
use function fclose;
use function fgets;
use function file_exists;
use function fopen;
use function fwrite;
use function is_array;
use function stream_set_blocking;
use function touch;
use function unlink;

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
