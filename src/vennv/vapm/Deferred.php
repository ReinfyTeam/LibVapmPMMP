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

use Generator;
use function call_user_func;
use function count;

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