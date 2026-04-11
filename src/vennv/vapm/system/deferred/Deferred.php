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
use vennv\vapm\utils\exceptions\DeferredException;
use vennv\vapm\utils\exceptions\Error;
use function call_user_func;
use function count;

final class Deferred implements DeferredInterface {
	protected mixed $return = null;

	protected ChildCoroutine $childCoroutine;

	public function __construct(callable $callback) {
		$generator = call_user_func($callback);
		$generator instanceof Generator ? $this->childCoroutine = new ChildCoroutine($generator) : throw new DeferredException(Error::DEFERRED_CALLBACK_MUST_RETURN_GENERATOR);
	}

	public function await() : Generator {
		while (!$this->childCoroutine->isFinished()) {
			$this->childCoroutine->run();
			yield;
		}

		$this->return = $this->childCoroutine->getReturn();

		return $this->return;
	}

	public static function awaitAll(DeferredInterface ...$deferreds) : Generator {
		$result = [];

		while (count($result) <= count($deferreds)) {
			foreach ($deferreds as $index => $deferred) {
				$childCoroutine = $deferred->getChildCoroutine();

				if ($childCoroutine->isFinished()) {
					$result[] = $childCoroutine->getReturn();
					unset($deferreds[$index]);
				} else {
					$childCoroutine->run();
				}
				yield;
			}

			yield;
		}

		return $result;
	}

	public static function awaitAny(DeferredInterface ...$deferreds) : Generator {
		$result = [];

		while (count($result) <= count($deferreds)) {
			foreach ($deferreds as $deferred) {
				$childCoroutine = $deferred->getChildCoroutine();

				if ($childCoroutine->isFinished()) {
					$result[] = $childCoroutine->getReturn();
					$deferreds = [];
					break;
				} else {
					$childCoroutine->run();
				}
				yield;
			}

			yield;
		}

		return $result;
	}

	public function isFinished() : bool {
		return $this->childCoroutine->isFinished();
	}

	public function getChildCoroutine() : ChildCoroutine {
		return $this->childCoroutine;
	}

	public function getComplete() : mixed {
		return $this->return;
	}
}
