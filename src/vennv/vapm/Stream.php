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

use Throwable;
use function array_shift;
use function array_unshift;
use function fclose;
use function fgets;
use function file_exists;
use function fopen;
use function fwrite;
use function is_array;
use function stream_set_blocking;
use function touch;
use function unlink;

interface StreamInterface {
	/**
	 * @throws Throwable
	 *
	 * Use this to read a file or url.
	 */
	public static function read(string $path) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to write to a file.
	 */
	public static function write(string $path, string $data) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to append to a file.
	 */
	public static function append(string $path, string $data) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to delete a file.
	 */
	public static function delete(string $path) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to create a file.
	 */
	public static function create(string $path) : Promise;

	/**
	 * @throws Throwable
	 *
	 * Use this to create a file or overwrite a file.
	 */
	public static function overWrite(string $path, string $data) : Promise;

	/**
	 * @param array<int|string, mixed> $array
	 * @throws Throwable
	 *
	 * Use this to flatten an array.
	 */
	public static function flattenArray(array $array) : Promise;
}

final class Stream implements StreamInterface {
	/**
	 * @throws Throwable
	 */
	public static function read(string $path) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($path) : void {
			$lines = '';
			$handle = fopen($path, 'r');

			if ($handle === false) {
				$reject(Error::UNABLE_TO_OPEN_FILE);
			} else {
				stream_set_blocking($handle, false);

				while (($line = fgets($handle)) !== false) {
					$lines .= $line;
					FiberManager::wait();
				}

				fclose($handle);
			}

			$resolve($lines);
		});
	}

	/**
	 * @throws Throwable
	 */
	public static function write(string $path, string $data) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($path, $data) : void {
			System::setTimeout(function () use ($resolve, $reject, $path, $data) : void {
				$callback = function (string $path, string $data) use ($reject) : void {
					$handle = fopen($path, 'w');

					if ($handle === false) {
						$reject(Error::UNABLE_TO_OPEN_FILE);
					} else {
						stream_set_blocking($handle, false);
						fwrite($handle, $data);
						fclose($handle);
					}
				};

				$callback($path, $data);
				$resolve();
			}, 0);
		});
	}

	/**
	 * @throws Throwable
	 */
	public static function append(string $path, string $data) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($path, $data) : void {
			System::setTimeout(function () use ($resolve, $reject, $path, $data) : void {
				$callback = function (string $path, string $data) use ($reject) : void {
					$handle = fopen($path, 'a');

					if ($handle === false) {
						$reject(Error::UNABLE_TO_OPEN_FILE);
					} else {
						stream_set_blocking($handle, false);
						fwrite($handle, $data);
						fclose($handle);
					}
				};

				$callback($path, $data);
				$resolve();
			}, 0);
		});
	}

	/**
	 * @throws Throwable
	 */
	public static function delete(string $path) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($path) : void {
			System::setTimeout(function () use ($resolve, $reject, $path) : void {
				$callback = function (string $path) use ($reject) : void {
					file_exists($path) ? unlink($path) : $reject(Error::FILE_DOES_NOT_EXIST);
				};
				$callback($path);
				$resolve();
			}, 0);
		});
	}

	/**
	 * @throws Throwable
	 */
	public static function create(string $path) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($path) : void {
			System::setTimeout(function () use ($resolve, $reject, $path) : void {
				$callback = function (string $path) use ($reject) : void {
					!file_exists($path) ? touch($path) : $reject(Error::FILE_ALREADY_EXISTS);
				};
				$callback($path);
				$resolve();
			}, 0);
		});
	}

	/**
	 * @throws Throwable
	 */
	public static function overWrite(string $path, string $data) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($path, $data) : void {
			System::setTimeout(function () use ($resolve, $reject, $path, $data) : void {
				$callback = function (string $path, string $data) use ($reject) : void {
					$handle = fopen($path, 'w+');
					if ($handle === false) {
						$reject(Error::UNABLE_TO_OPEN_FILE);
					} else {
						stream_set_blocking($handle, false);
						fwrite($handle, $data);
						fclose($handle);
					}
				};

				$callback($path, $data);
				$resolve();
			}, 0);
		});
	}

	/**
	 * @param array<int|string, mixed> $array
	 * @throws Throwable
	 */
	public static function flattenArray(array $array) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($array) : void {
			$result = [];
			$stack = [$array];

			while (!empty($stack)) {
				$element = array_shift($stack);
				if ($element === null) {
					$reject(Error::INVALID_ARRAY);
					break;
				}

				foreach ($element as $value) {
					is_array($value) ? array_unshift($stack, $value) : $result[] = $value;
					FiberManager::wait();
				}
				FiberManager::wait();
			}

			$resolve($result);
		});
	}
}
