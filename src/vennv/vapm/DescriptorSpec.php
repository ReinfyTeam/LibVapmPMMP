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