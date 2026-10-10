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

use function gc_collect_cycles;

interface GarbageCollectionInterface {
	/**
	 * Set the limit of the loop to collect garbage
	 */
	public function setLimitLoop(int $limit) : void;

	/**
	 * Collect garbage with limit
	 */
	public function collectWL() : void;

	/**
	 * Collect garbage
	 */
	public static function collect() : void;
}

final class GarbageCollection implements GarbageCollectionInterface {
	private int $countLoop = 0;

	/**
	 * @param int $limitLoop The limit of the loop to collect garbage
	 */
	public function __construct(private int $limitLoop = 1000) {
		// TODO: Implement __construct() method.
	}

	public function setLimitLoop(int $limit) : void {
		$this->limitLoop = $limit;
	}

	public function collectWL() : void {
		if ($this->countLoop >= $this->limitLoop) {
			gc_collect_cycles();
			$this->countLoop = 0;
		} else {
			++$this->countLoop;
		}
	}

	public static function collect() : void {
		gc_collect_cycles();
	}
}
