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

use Throwable;
use function array_shift;
use function call_user_func;
use function count;

/**
 * Class Worker
 * @package vennv\vapm
 *
 * This class is used to create a worker to run the work.
 * All asynchronous methods are based on this class.
 */
interface WorkerInterface {
	/**
	 * @return bool
	 *
	 * Check the worker is started.
	 */
	public function isStarted() : bool;

	/**
	 * @return Work
	 *
	 * Get the work.
	 */
	public function getWork() : Work;

	/**
	 * @return void
	 *
	 * This is method help you to remove the worker from the worker list.
	 * You should call this method when the work is done to avoid memory leaks.
	 */
	public function done() : void;

	/**
	 * @return void
	 *
	 * Collect the result of the work.
	 */
	public function collect(mixed $result) : void;

	/**
	 * @return array<int|string, mixed>
	 *
	 * Get the result of the work.
	 */
	public function get() : array;

	/**
	 * @return bool
	 *
	 * Check the worker is locked.
	 */
	public function isLocked() : bool;

	/**
	 * @return void
	 *
	 * Lock the worker.
	 */
	public function lock() : void;

	/**
	 * @return void
	 *
	 * Unlock the worker.
	 */
	public function unlock() : void;

	/**
	 * @return void
	 *
	 * Add a child worker to the parent worker.
	 */
	public function addWorker(Worker $worker, callable $callback) : void;

	/**
	 * @return Async
	 *
	 * Run the work.
	 * @throws Throwable
	 */
	public function run(callable $callback) : Async;
}

final class Worker implements WorkerInterface {
	private const LOCKED = "locked";

	public bool $isStarted = false;

	public bool $isChild = false;

	protected static int $nextId = 0;

	public int $id;

	/** @var array<string, mixed> */
	private array $options;

	/** @var array<int, array<Worker|callable>> */
	private array $childWorkers = [];

	/** @var array<int|string, array<int|string, mixed>> */
	private static array $workers = [];

	private Work $work;

	/**
	 * @param array<string, mixed> $options
	 */
	public function __construct(Work $work, array $options = ["threads" => 4]) {
		$this->work = $work;
		$this->options = $options;
		$this->id = $this->generateId();
		self::$workers[$this->id] = [];
	}

	private function generateId() : int {
		if (self::$nextId >= PHP_INT_MAX) {
			self::$nextId = 0;
		}
		return self::$nextId++;
	}

	public function isStarted() : bool {
		return $this->isStarted;
	}

	public function getWork() : Work {
		return $this->work;
	}

	public function done() : void {
		if ($this->isChild) {
			return;
		}
		unset(self::$workers[$this->id]);
	}

	public function collect(mixed $result) : void {
		self::$workers[$this->id][] = $result;
	}

	/**
	 * @return array<int|string, mixed>
	 */
	public function get() : array {
		return self::$workers[$this->id];
	}

	public function isLocked() : bool {
		return isset(self::$workers[$this->id][self::LOCKED]);
	}

	public function lock() : void {
		self::$workers[$this->id][self::LOCKED] = true;
	}

	public function unlock() : void {
		unset(self::$workers[$this->id][self::LOCKED]);
	}

	public function addWorker(Worker $worker, callable $callback) : void {
		$worker->isChild = true;
		$this->childWorkers[] = [$worker, $callback];
	}

	/**
	 * @throws Throwable
	 */
	public function run(callable $callback) : Async {
		$this->isStarted = true;
		$work = $this->getWork();

		return new Async(function () use ($work, $callback) : void {
			$threads = $this->options["threads"];
			$producerChunkSize = Settings::getWorkerProducerChunkSize();

			if ($threads >= 1) {
				$promises = [];
				$totalCountWorks = $work->count();
				$pendingQueue = [];

				$gc = new GarbageCollection();
				while ($this->isLocked() || $totalCountWorks > 0) {
					if (!$this->isLocked()) {
						if (count($promises) < $threads && ($work->count() > 0 || $pendingQueue !== [])) {
							if ($pendingQueue === [] && $work->count() > 0) {
								$pendingQueue = $work->dequeueChunk($producerChunkSize);
							}

							if ($pendingQueue !== []) {
								/** @var ClosureThread $callbackQueue */
								$callbackQueue = array_shift($pendingQueue);
								if ($callbackQueue instanceof ClosureThread) {
									$promises[] = $callbackQueue->start();
								}
							}
						} else {
							/** @var Promise $promise */
							foreach ($promises as $index => $promise) {
								$result = EventLoop::getReturn($promise->getId());
								if ($result !== null) {
									$result = $promise->getResult();
									$this->collect($result);
									unset($promises[$index]);
									$totalCountWorks--;
								}
							}
						}
					}
					$gc->collectWL();
					FiberManager::wait();
				}

				while (count($this->childWorkers) > 0) {
					$childWorker = array_shift($this->childWorkers);
					if ($childWorker !== null) {
						/** @var WorkerInterface $worker */
						$worker = $childWorker[0];

						/** @var callable $workerCallback */
						$workerCallback = $childWorker[1];

						Async::await($worker->run($workerCallback));

						$this->collect($worker->get());
						$worker->done();
					}
					FiberManager::wait();
					$gc->collectWL();
				}

				$data = Async::await(Stream::flattenArray($this->get()));
				call_user_func($callback, $data, $this);
			}
		});
	}
}
