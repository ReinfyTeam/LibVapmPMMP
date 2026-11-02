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

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Throwable;
use vennv\vapm\utils\Utils;
use function array_map;
use function array_values;
use function fclose;
use function feof;
use function fgets;
use function fwrite;
use function implode;
use function is_array;
use function is_bool;
use function is_callable;
use function is_resource;
use function is_string;
use function json_decode;
use function json_encode;
use function microtime;
use function proc_close;
use function proc_get_status;
use function proc_open;
use function proc_terminate;
use function rtrim;
use function spl_object_id;
use function str_contains;
use function str_replace;
use function stream_get_contents;
use function stream_select;
use function stream_set_blocking;
use function stream_set_write_buffer;
use function strlen;
use const PHP_BINARY;
use const PHP_EOL;
use const STDIN;
use const STDOUT;

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
