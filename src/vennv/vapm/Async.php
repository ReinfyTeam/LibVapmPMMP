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

use RuntimeException;
use Throwable;
use vennv\vapm\utils\Utils;
use function is_callable;

interface AsyncInterface {
	public function getId() : int;

	/**
	 * @throws Throwable
	 */
	public static function await(mixed $await) : mixed;
}

final class Async implements AsyncInterface {
	private Promise $promise;

	/**
	 * @throws Throwable
	 */
	public function __construct(callable $callback) {
		$promise = new Promise($callback, true);
		$this->promise = $promise;
	}

	public function getId() : int {
		return $this->promise->getId();
	}

	/**
	 * @param Async|Promise|callable|mixed $await
	 * @throws Throwable
	 */
	public static function await(mixed $await) : mixed {
		if (!$await instanceof Promise && !$await instanceof Async) {
			if (is_callable($await)) {
				$await = new Async($await);
			} else {
				if (!Utils::isClass(Async::class)) {
					throw new RuntimeException(Error::ASYNC_AWAIT_MUST_CALL_IN_ASYNC_FUNCTION);
				}
				return $await;
			}
		}

		do {
			$return = EventLoop::getReturn($await->getId());
			if ($return === null) {
				FiberManager::wait();
			}
		} while ($return === null);

		return $return->getResult();
	}
}
