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
use SplQueue;
use function array_values;

interface WorkInterface {
	/**
	 * @param array<int, mixed> $args
	 * @return void
	 *
	 * The work is a function that will be executed when the work is run.
	 */
	public function add(callable $work, array $args = []) : void;

	/**
	 * @return void
	 *
	 * Remove the work from the work list.
	 */
	public function remove(int $index) : void;

	/**
	 * @return void
	 *
	 * Remove all works from the work list.
	 */
	public function clear() : void;

	/**
	 * @return int
	 *
	 * Get the number of works in the work list.
	 */
	public function count() : int;

	/**
	 * @return bool
	 *
	 * Check if the work list is empty.
	 */
	public function isEmpty() : bool;

	/**
	 * @return mixed
	 *
	 * Get the first work in the work list.
	 */
	public function dequeue() : mixed;

	/**
	 * @return Generator
	 *
	 * Get the work list by number.
	 */
	public function getArrayByNumber(int $number) : Generator;

	/**
	 * @return Generator
	 *
	 * Get all works in the work list.
	 */
	public function getAll() : Generator;

	/**
	 * @return void
	 *
	 * Run all works in the work list.
	 */
	public function run() : void;
}

final class Work implements WorkInterface {
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
