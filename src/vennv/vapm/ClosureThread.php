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

use Generator;
use function call_user_func;
use function is_array;
use function is_bool;
use function is_callable;
use function is_null;
use function is_object;
use function iterator_to_array;
use function json_encode;

interface ClosureThreadInterface {
	/**
	 * @return void
	 *
	 * This function runs the callback function for the thread.
	 */
	public function onRun() : void;
}

final class ClosureThread extends Thread implements ClosureThreadInterface {
	private mixed $callback;

	/**
	 * @var array<int|float|array|object|null, mixed>
	 * @phpstan-var array<int|float|array|object|null, mixed>
	 */
	private array $argsCallback = [];

	/**
	 * @param array<int|float|array|object|null, mixed> $args
	 */
	public function __construct(callable $callback, array $args = []) {
		$this->callback = $callback;
		$this->argsCallback = $args;
		parent::__construct($callback, $args);
	}

	public function onRun() : void {
		if (is_callable($this->callback)) {
			$callback = call_user_func($this->callback, ...$this->argsCallback);
			if ($callback instanceof Generator) {
				$callback = function () use ($callback) : Generator {
					yield from $callback;
				};
				$callback = call_user_func($callback, ...$this->argsCallback);
			}
			if (is_array($callback)) {
				$callback = json_encode($callback);
			} elseif (is_object($callback) && !$callback instanceof Generator) {
				$callback = json_encode($callback);
			} elseif (is_bool($callback)) {
				$callback = $callback ? 'true' : 'false';
			} elseif (is_null($callback)) {
				$callback = 'null';
			} elseif ($callback instanceof Generator) {
				$callback = json_encode(iterator_to_array($callback));
			} else {
				$callback = (string) $callback;
			}
			if (is_bool($callback)) {
				$callback = (string) $callback;
			}
			self::post($callback);
		}
	}
}