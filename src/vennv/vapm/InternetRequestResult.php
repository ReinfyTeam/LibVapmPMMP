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

interface InternetRequestResultInterface {
	/**
	 * @return string[][]
	 */
	public function getHeaders() : array;

	public function getBody() : string;

	public function getCode() : int;
}

final class InternetRequestResult implements InternetRequestResultInterface {
	/** @var string[][] $headers */
	private array $headers;

	private string $body;

	private int $code;

	/**
	 * @param string[][] $headers
	 */
	public function __construct(array $headers, string $body, int $code) {
		$this->headers = $headers;
		$this->body = $body;
		$this->code = $code;
	}

	/**
	 * @return string[][]
	 */
	public function getHeaders() : array {
		return $this->headers;
	}

	public function getBody() : string {
		return $this->body;
	}

	public function getCode() : int {
		return $this->code;
	}
}
