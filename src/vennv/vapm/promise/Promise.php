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

namespace vennv\vapm\promise;

use ArrayObject;
use Fiber;
use Throwable;
use vennv\vapm\FiberManager;
use vennv\vapm\system\event\EventLoop;
use vennv\vapm\system\Settings;
use vennv\vapm\system\System;
use vennv\vapm\thread\async\Async;
use function call_user_func;
use function count;
use function is_callable;
use function microtime;

final class Promise implements PromiseInterface {
	private int $id;

	private float $timeOut = 0.0;

	private float $timeEnd = 0.0;

	private mixed $result = null;

	private mixed $return = null;

	private string $status = StatusPromise::PENDING;

	private ArrayObject $callbacksResolve;

	/** @var callable(mixed):mixed $callbackReject */
	private mixed $callbackReject;

	/** @var callable():void $callbackFinally */
	private mixed $callbackFinally;

	private float $timeStart;

	private Fiber $fiber;

	/** @var callable $callback */
	private mixed $callback;

	private bool $justGetResult;

	/**
	 * @throws Throwable
	 */
	public function __construct(callable $callback, bool $justGetResult = false) {
		System::init();

		$this->callbacksResolve = new ArrayObject();
		$this->id = EventLoop::generateId();
		$this->callback = $callback;
		$this->fiber = new Fiber($callback);

		if ($justGetResult) {
			$this->result = $this->fiber->start();
		} else {
			$resolve = function ($result = '') : void {
				$this->resolve($result);
			};

			$reject = function ($result = '') : void {
				$this->reject($result);
			};

			$this->fiber->start($resolve, $reject);
		}

		if (!$this->fiber->isTerminated()) {
			FiberManager::wait();
		}

		$this->justGetResult = $justGetResult;

		$this->timeStart = microtime(true);

		$this->callbacksResolve->offsetSet("master", function ($result) : mixed {
			return $result;
		});

		$this->callbackReject = function ($result) : mixed {
			return $result;
		};

		$this->callbackFinally = function () : void {
		};

		EventLoop::addQueue($this);
	}

	public function setResult(mixed $result) : Promise {
		$this->result = $result;
		return $this;
	}

	/**
	 * @throws Throwable
	 */
	public static function c(callable $callback, bool $justGetResult = false) : Promise {
		return new self($callback, $justGetResult);
	}

	public function getId() : int {
		return $this->id;
	}

	public function getFiber() : Fiber {
		return $this->fiber;
	}

	public function isJustGetResult() : bool {
		return $this->justGetResult;
	}

	public function getTimeOut() : float {
		return $this->timeOut;
	}

	public function getTimeStart() : float {
		return $this->timeStart;
	}

	public function getTimeEnd() : float {
		return $this->timeEnd;
	}

	public function setTimeEnd(float $timeEnd) : void {
		$this->timeEnd = $timeEnd;
	}

	public function canDrop() : bool {
		return microtime(true) - $this->timeEnd > Settings::TIME_DROP;
	}

	public function getStatus() : string {
		return $this->status;
	}

	public function isPending() : bool {
		return $this->status === StatusPromise::PENDING;
	}

	public function isResolved() : bool {
		return $this->status === StatusPromise::FULFILLED;
	}

	public function isRejected() : bool {
		return $this->status === StatusPromise::REJECTED;
	}

	public function getResult() : mixed {
		return $this->result;
	}

	public function getReturn() : mixed {
		return $this->return;
	}

	public function getCallback() : callable {
		return $this->callback;
	}

	public function resolve(mixed $value = '') : void {
		if ($this->isPending()) {
			$this->status = StatusPromise::FULFILLED;
			$this->result = $value;
		}
	}

	public function reject(mixed $value = '') : void {
		if ($this->isPending()) {
			$this->status = StatusPromise::REJECTED;
			$this->result = $value;
		}
	}

	/**
	 * @param callable(mixed):mixed $callback
	 */
	public function then(callable $callback) : Promise {
		$this->callbacksResolve[] = $callback;
		return $this;
	}

	/**
	 * @param callable(mixed):mixed $callback
	 */
	public function catch(callable $callback) : Promise {
		$this->callbackReject = $callback;
		return $this;
	}

	/**
	 * @param callable():void $callback
	 */
	public function finally(callable $callback) : Promise {
		$this->callbackFinally = $callback;
		return $this;
	}

	/**
	 * @throws Throwable
	 */
	public function useCallbacks() : void {
		$result = $this->result;

		if ($this->isResolved()) {
			$callbacks = $this->callbacksResolve;

			/** @var callable $master */
			$master = $callbacks["master"] ?? null;

			if (is_callable($master)) {
				$this->result = call_user_func($master, $result);
				unset($callbacks["master"]);
			}

			if (count($callbacks) > 0) {
				/** @var callable $callback */
				$callback = $callbacks[0];
				$resultFirstCallback = call_user_func($callback, $this->result);
				$this->result = $resultFirstCallback;
				$this->return = $resultFirstCallback;
				$this->checkStatus($callbacks, $this->return);
			}
		} elseif ($this->isRejected()) {
			if (is_callable($this->callbackReject)) {
				$this->result = call_user_func($this->callbackReject, $result);
			}
		}
	}

	private function checkStatus(ArrayObject $callbacks, mixed $return) : void {
		$lastPromise = null;

		while (count($callbacks) > 0) {
			$cancel = false;

			/**
			 * @var int|string $case
			 * @var callable $callback
			 */
			foreach ($callbacks->getArrayCopy() as $case => $callback) {
				if ($return === null) {
					$cancel = true;
					break;
				}

				if ($case !== 0 && $return instanceof Promise) {
					EventLoop::addQueue($return);
					$return->then($callback);
					if (is_callable($this->callbackReject)) {
						$return->catch($this->callbackReject);
					}
					$lastPromise = $return;

					$callbacks->offsetUnset($case);
					continue;
				}

				if (count($callbacks) === 1) {
					$cancel = true;
				}
			}

			if ($cancel) {
				break;
			}
		}

		if ($lastPromise !== null) {
			$lastPromise->finally($this->callbackFinally);
		} else {
			if (is_callable($this->callbackFinally)) {
				call_user_func($this->callbackFinally);
			}
		}
	}

	/**
	 * @param array<int, Async|Promise|callable> $promises
	 * @phpstan-param array<int, Async|Promise|callable> $promises
	 * @throws Throwable
	 */
	public static function all(array $promises) : Promise {
		$promise = new Promise(function (callable $resolve, callable $reject) use ($promises) : void {
			$count = count($promises);
			$results = [];
			$isSolved = false;

			while (!$isSolved) {
				foreach ($promises as $index => $promise) {
					if (is_callable($promise)) {
						$promise = new Async($promise);
					}

					if ($promise instanceof Async || $promise instanceof Promise) {
						$return = EventLoop::getReturn($promise->getId());

						if ($return?->isRejected() === true) {
							$reject($return->getResult());
							$isSolved = true;
							break;
						}

						if ($return?->isResolved() === true) {
							$results[] = $return->getResult();
							unset($promises[$index]);
						}
					}

					if (count($results) === $count) {
						$resolve($results);
						$isSolved = true;
					}
					FiberManager::wait();
				}

				if (!$isSolved) {
					FiberManager::wait();
				}
			}
		});

		return $promise;
	}

	/**
	 * @param array<int, Async|Promise|callable> $promises
	 * @phpstan-param array<int, Async|Promise|callable> $promises
	 * @throws Throwable
	 */
	public static function allSettled(array $promises) : Promise {
		$promise = new Promise(function (callable $resolve) use ($promises) : void {
			$count = count($promises);
			$results = [];
			$isSolved = false;

			while (!$isSolved) {
				foreach ($promises as $index => $promise) {
					if (is_callable($promise)) {
						$promise = new Async($promise);
					}

					if ($promise instanceof Async || $promise instanceof Promise) {
						$return = EventLoop::getReturn($promise->getId());

						if ($return !== null) {
							$results[] = new PromiseResult($return->getStatus(), $return->getResult());
							unset($promises[$index]);
						}
					}

					if (count($results) === $count) {
						$resolve($results);
						$isSolved = true;
					}
					FiberManager::wait();
				}

				if (!$isSolved === false) {
					FiberManager::wait();
				}
			}
		});

		return $promise;
	}

	/**
	 * @param array<int, Async|Promise|callable> $promises
	 * @phpstan-param array<int, Async|Promise|callable> $promises
	 * @throws Throwable
	 */
	public static function any(array $promises) : Promise {
		$promise = new Promise(function (callable $resolve, callable $reject) use ($promises) : void {
			$count = count($promises);
			$results = [];
			$isSolved = false;

			while ($isSolved === false) {
				foreach ($promises as $index => $promise) {
					if (is_callable($promise)) {
						$promise = new Async($promise);
					}

					if ($promise instanceof Async || $promise instanceof Promise) {
						$return = EventLoop::getReturn($promise->getId());

						if ($return?->isRejected() === true) {
							$results[] = $return->getResult();
							unset($promises[$index]);
						}

						if ($return?->isResolved() === true) {
							$resolve($return->getResult());
							$isSolved = true;
							break;
						}
					}

					if (count($results) === $count) {
						$reject($results);
						$isSolved = true;
					}
					FiberManager::wait();
				}

				if ($isSolved === false) {
					FiberManager::wait();
				}
			}
		});

		return $promise;
	}

	/**
	 * @param array<int, Async|Promise|callable> $promises
	 * @phpstan-param array<int, Async|Promise|callable> $promises
	 * @throws Throwable
	 */
	public static function race(array $promises) : Promise {
		$promise = new Promise(function (callable $resolve, callable $reject) use ($promises) : void {
			$isSolved = false;

			while ($isSolved === false) {
				foreach ($promises as $promise) {
					if (is_callable($promise)) {
						$promise = new Async($promise);
					}
					if ($promise instanceof Async || $promise instanceof Promise) {
						$return = EventLoop::getReturn($promise->getId());

						if ($return?->isRejected() === true) {
							$reject($return->getResult());
							$isSolved = true;
							break;
						}

						if ($return?->isResolved() === true) {
							$resolve($return->getResult());
							$isSolved = true;
							break;
						}
					}
					FiberManager::wait();
				}

				if ($isSolved === false) {
					FiberManager::wait();
				}
			}
		});

		return $promise;
	}
}
