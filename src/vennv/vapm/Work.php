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
use SplQueue;
use function array_values;

final class Work implements WorkInterface {
	/** @var SplQueue<ClosureThread> */
	private SplQueue $queue;

	public function __construct() {
		$this->queue = new SplQueue();
	}

	/**
	 * @param array<int, mixed> $args
	 * @return void
	 *
	 * Add a work to the work list.
	 */
	public function add(callable $work, array $args = []) : void {
		$this->queue->enqueue(new ClosureThread($work, array_values($args)));
	}

	public function remove(int $index) : void {
		$this->queue->offsetUnset($index);
	}

	public function clear() : void {
		$this->queue = new SplQueue();
	}

	public function count() : int {
		return $this->queue->count();
	}

	public function isEmpty() : bool {
		return $this->queue->isEmpty();
	}

	public function dequeue() : mixed {
		return $this->queue->dequeue();
	}

	public function getArrayByNumber(int $number) : Generator {
		for ($i = 0; $i < $number; $i++) {
			yield $this->queue->dequeue();
		}
	}

	public function getAll() : Generator {
		while (!$this->queue->isEmpty()) {
			yield $this->queue->dequeue();
		}
	}

	public function run() : void {
		$gc = new GarbageCollection();
		while (!$this->queue->isEmpty()) {
			/** @var ClosureThread $work */
			$work = $this->queue->dequeue();
			$work->start();
			$gc->collectWL();
		}
	}
}
