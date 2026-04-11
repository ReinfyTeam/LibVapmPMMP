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

namespace vennv\vapm\system\deferred;

use Generator;
use vennv\vapm\coroutine\ChildCoroutine;

/**
 * @author  VennDev <venn.dev@gmail.com>
 * @package vennv\vapm
 *
 * This interface is used to create a deferred object that can be used to get the result of a coroutine.
 */
interface DeferredInterface {
	/**
	 * This method is used to get the result of the deferred.
	 */
	public function await() : Generator;

	/**
	 * @return Generator
	 *
	 * This method is used to get all the results of the deferred.
	 */
	public static function awaitAll(DeferredInterface ...$deferreds) : Generator;

	/**
	 * @return Generator
	 *
	 * This method is used to get the first result of the deferred.
	 */
	public static function awaitAny(DeferredInterface ...$deferreds) : Generator;

	/**
	 * This method is used to check if the deferred is finished.
	 */
	public function isFinished() : bool;

	/**
	 * This method is used to get the child coroutine of the deferred.
	 */
	public function getChildCoroutine() : ChildCoroutine;

	/**
	 * This method is used to get the result of the deferred.
	 */
	public function getComplete() : mixed;
}
