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

final class DescriptorSpec {
	public const BASIC = [
		0 => ['pipe', 'r'],
		1 => ['pipe', 'w'],
		2 => ['pipe', 'w']
	];

	public const IGNORE_STDIN = [
		0 => ['file', '/dev/null', 'r'],
		1 => ['pipe', 'w'],
		2 => ['pipe', 'w']
	];

	public const IGNORE_STDOUT = [
		0 => ['pipe', 'r'],
		1 => ['file', '/dev/null', 'w'],
		2 => ['pipe', 'w']
	];

	public const IGNORE_STDERR = [
		0 => ['pipe', 'r'],
		1 => ['pipe', 'w'],
		2 => ['file', '/dev/null', 'w']
	];

	public const IGNORE_STDOUT_AND_STDERR = [
		0 => ['pipe', 'r'],
		1 => ['file', '/dev/null', 'w'],
		2 => ['file', '/dev/null', 'w']
	];

	public const IGNORE_STDIN_AND_STDERR = [
		0 => ['file', '/dev/null', 'r'],
		1 => ['pipe', 'w'],
		2 => ['file', '/dev/null', 'w']
	];

	public const IGNORE_STDIN_AND_STDOUT = [
		0 => ['file', '/dev/null', 'r'],
		1 => ['file', '/dev/null', 'w'],
		2 => ['pipe', 'w']
	];
}