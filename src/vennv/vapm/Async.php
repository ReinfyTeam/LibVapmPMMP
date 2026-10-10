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