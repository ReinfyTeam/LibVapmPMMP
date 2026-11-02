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

interface ThreadInterface {
	/**
	 * This abstract method use to run the thread
	 */
	public function onRun() : void;

	/**
	 * @param array<int, list<string>|resource> $mode
	 * @throws ReflectionException
	 * @throws Throwable
	 * @phpstan-param array<int, list<string>|resource> $mode
	 *
	 * This method use to start the thread
	 */
	public function start(array $mode = DescriptorSpec::BASIC) : Promise;
}
