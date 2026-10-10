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

use TypeError;

final class ThreadException extends TypeError {
	public function __construct(
		protected string $errorMessage,
		protected int    $errorCode = 0
	) {
		parent::__construct($this->errorMessage, $this->errorCode);
	}

	public function __toString() : string {
		return __CLASS__ . ": [$this->errorCode]: $this->errorMessage\n";
	}
}