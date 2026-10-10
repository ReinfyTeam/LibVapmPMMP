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

interface PromiseResultInterface {
	public function getStatus() : string;

	public function getResult() : mixed;
}

final class PromiseResult implements PromiseResultInterface {
	private string $status;

	private mixed $result;

	public function __construct(string $status, mixed $result) {
		$this->status = $status;
		$this->result = $result;
	}

	public function getStatus() : string {
		return $this->status;
	}

	public function getResult() : mixed {
		return $this->result;
	}
}