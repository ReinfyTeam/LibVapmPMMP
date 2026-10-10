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

use Exception;
use Generator;

interface ChildCoroutineInterface {
	/**
	 * @return void
	 *
	 * This function sets the exception.
	 */
	public function setException(Exception $exception) : void;

	/**
	 * @return ChildCoroutine
	 *
	 * This function runs the coroutine.
	 */

	public function run() : ChildCoroutine;

	/**
	 * @return bool
	 *
	 * This function checks if the coroutine is finished.
	 */
	public function isFinished() : bool;

	/**
	 * @return mixed
	 *
	 * This function returns the return value of the coroutine.
	 */
	public function getReturn() : mixed;
}

final class ChildCoroutine implements ChildCoroutineInterface {
	protected Generator $coroutine;

	protected Exception $exception;

	public function __construct(Generator $coroutine) {
		$this->coroutine = $coroutine;
	}

	public function setException(Exception $exception) : void {
		$this->exception = $exception;
	}

	public function run() : ChildCoroutine {
		$this->coroutine->send($this->coroutine->current());
		return $this;
	}

	public function isFinished() : bool {
		return !$this->coroutine->valid();
	}

	public function getReturn() : mixed {
		return $this->coroutine->getReturn();
	}
}