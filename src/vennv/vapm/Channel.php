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
use RuntimeException;
use function array_shift;

interface ChannelInterface {
	/**
	 * @param mixed $message
	 * @return Generator
	 *
	 * This function is used to send a message to the channel.
	 */
	public function sendGen($message) : Generator;

	/**
	 * @param mixed $message
	 * @return void
	 *
	 * This function is used to send a message to the channel.
	 */
	public function send($message) : void;

	/**
	 * @return Generator
	 *
	 * This function is used to receive a message from the channel.
	 */
	public function receiveGen(callable $callback) : Generator;

	/**
	 * @return void
	 *
	 * This function is used to receive a message from the channel.
	 */
	public function receive(callable $callback) : void;

	/**
	 * @return bool
	 *
	 * This function is used to check if the channel is empty.
	 */
	public function isEmpty() : bool;

	/**
	 * @return void
	 *
	 * This function is used to close the channel.
	 */
	public function close() : void;

	/**
	 * @return bool
	 *
	 * This function is used to check if the channel is closed.
	 */
	public function isClosed() : bool;
}

final class Channel implements ChannelInterface {
	/** @var mixed[] */
	private array $queue = [];

	private bool $locked = false;

	private bool $closed = false;

	public function sendGen($message) : Generator {
		$this->exceptionIfClosed();
		while ($this->locked) {
			yield;
		}
		$this->locked = true;
		$this->queue[] = $message;
		$this->locked = false;
	}

	public function send($message) : void {
		$this->exceptionIfClosed();
		while ($this->locked) {
			CoroutineGen::run();
		}
		$this->locked = true;
		$this->queue[] = $message;
		$this->locked = false;
	}

	public function receiveGen(callable $callback) : Generator {
		while (!$this->closed || !empty($this->queue)) {
			$message = array_shift($this->queue);
			if ($message !== null) {
				$callback($message);
			}
			yield;
		}
	}

	public function receive(callable $callback) : void {
		while (!$this->closed || !empty($this->queue)) {
			$message = array_shift($this->queue);
			if ($message !== null) {
				$callback($message);
			}
			CoroutineGen::run();
		}
	}

	public function isEmpty() : bool {
		return empty($this->queue);
	}

	public function close() : void {
		$this->closed = true;
	}

	public function isClosed() : bool {
		return $this->closed;
	}

	private function exceptionIfClosed() : void {
		if ($this->closed) {
			throw new RuntimeException('Channel is closed');
		}
	}
}
