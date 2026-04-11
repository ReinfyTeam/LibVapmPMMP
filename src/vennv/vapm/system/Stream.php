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

namespace vennv\vapm\system;

use Throwable;
use vennv\vapm\FiberManager;
use vennv\vapm\promise\Promise;
use vennv\vapm\utils\exceptions\Error;
use function array_shift;
use function array_unshift;
use function call_user_func;
use function fclose;
use function fgets;
use function file_exists;
use function fopen;
use function fwrite;
use function is_array;
use function stream_set_blocking;
use function touch;
use function unlink;

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
				$callback = function ($path, $data) use ($reject) : void {
					$handle = fopen($path, 'w');

					if ($handle === false) {
						$reject(Error::UNABLE_TO_OPEN_FILE);
					} else {
						stream_set_blocking($handle, false);
						fwrite($handle, $data);
						fclose($handle);
					}
				};

				call_user_func($callback, $path, $data);
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
				$callback = function ($path, $data) use ($reject) : void {
					$handle = fopen($path, 'a');

					if ($handle === false) {
						$reject(Error::UNABLE_TO_OPEN_FILE);
					} else {
						stream_set_blocking($handle, false);
						fwrite($handle, $data);
						fclose($handle);
					}
				};

				call_user_func($callback, $path, $data);
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
				$callback = function ($path) use ($reject) : void {
					file_exists($path) ? unlink($path) : $reject(Error::FILE_DOES_NOT_EXIST);
				};
				call_user_func($callback, $path);
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
				$callback = function ($path) use ($reject) : void {
					!file_exists($path) ? touch($path) : $reject(Error::FILE_ALREADY_EXISTS);
				};
				call_user_func($callback, $path);
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
				$callback = function ($path, $data) use ($reject) : void {
					$handle = fopen($path, 'w+');
					if ($handle === false) {
						$reject(Error::UNABLE_TO_OPEN_FILE);
					} else {
						stream_set_blocking($handle, false);
						fwrite($handle, $data);
						fclose($handle);
					}
				};

				call_user_func($callback, $path, $data);
				$resolve();
			}, 0);
		});
	}

	/**
	 * @param array<int|string, mixed> $array
	 * @throws Throwable
	 */
	public static function flattenArray(array $array) : Promise {
		return new Promise(function (callable $resolve, callable $reject) use ($array) {
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
