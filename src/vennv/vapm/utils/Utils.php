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

namespace vennv\vapm\utils;

use Closure;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;
use ReflectionFunction;
use RuntimeException;
use SplFileInfo;
use vennv\vapm\utils\exceptions\Error;
use function array_keys;
use function array_slice;
use function array_values;
use function count;
use function debug_backtrace;
use function explode;
use function file;
use function get_resource_type;
use function gettype;
use function implode;
use function intdiv;
use function is_array;
use function is_bool;
use function is_callable;
use function is_null;
use function is_object;
use function is_resource;
use function is_string;
use function json_decode;
use function json_encode;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function serialize;
use function str_replace;
use function strlen;
use function strpos;
use function strrpos;
use function substr;

final class Utils implements UtilsInterface {
	public static function milliSecsToSecs(float $milliSecs) : float {
		return $milliSecs / 1000;
	}

	/**
	 * @throws ReflectionException
	 */
	public static function closureToString(Closure $closure) : string {
		$reflection = new ReflectionFunction($closure);
		$startLine = $reflection->getStartLine();
		$endLine = $reflection->getEndLine();
		$filename = $reflection->getFileName();

		if ($filename === false || $startLine === false || $endLine === false) {
			throw new ReflectionException(Error::CANNOT_FIND_FUNCTION_KEYWORD);
		}

		$lines = file($filename);
		if ($lines === false) {
			throw new ReflectionException(Error::CANNOT_READ_FILE);
		}

		$result = implode("", array_slice($lines, $startLine - 1, $endLine - $startLine + 1));
		$startPos = strpos($result, 'function');
		if ($startPos === false) {
			$startPos = strpos($result, 'fn');
			if ($startPos === false) {
				throw new ReflectionException(Error::CANNOT_FIND_FUNCTION_KEYWORD);
			}
		}

		$endBracketPos = strrpos($result, '}');
		if ($endBracketPos === false) {
			throw new ReflectionException(Error::CANNOT_FIND_FUNCTION_KEYWORD);
		}

		return substr($result, $startPos, $endBracketPos - $startPos + 1);
	}

	/**
	 * @throws RuntimeException
	 */
	public static function closureToStringSafe(Closure $closure) : string {
		$input = self::closureToString($closure);
		$input = self::removeComments($input);

		if (!is_string($input)) {
			throw new RuntimeException(Error::INPUT_MUST_BE_STRING_OR_CALLABLE);
		}

		$input = self::outlineToInline($input);
		$input = self::fixInputCommand($input);

		return $input;
	}

	public static function getAllByDotFile(string $path, string $dotFile) : Generator {
		$dir = new RecursiveDirectoryIterator($path);
		$iterator = new RecursiveIteratorIterator($dir);

		foreach ($iterator as $file) {
			if ($file instanceof SplFileInfo && preg_match('%' . $dotFile . '$%', $file->getFilename()) === 1) {
				yield $file->getPathname();
			}
		}
	}

	public static function outlineToInline(string $text) : string {
		return str_replace(["\r", "\n", "\t", '  '], '', $text);
	}

	public static function fixInputCommand(string $text) : string {
		return str_replace('"', '\'', $text);
	}

	/**
	 * Remove comments from a string
	 */
	public static function removeComments(string $text) : ?string {
		$text = preg_replace('/(?<!:)\/\/.*?(\r\n|\n|$)/', '', $text);
		if ($text === null) {
			return null;
		}
		$text = preg_replace('/\/\*[\s\S]*?\*\//', '', $text);
		return $text;
	}

	/**
	 * @param mixed $data
	 *
	 * Get bytes of a string or object or array
	 */
	public static function getBytes(mixed $data) : int {
		if (is_string($data)) {
			return strlen($data);
		}
		if (is_object($data) || is_array($data)) {
			return strlen(serialize($data));
		}
		return 0;
	}

	/**
	 * @return Generator
	 *
	 * Split a string by slash
	 */
	public static function splitStringBySlash(string $string) : Generator {
		$parts = explode('/', $string);
		foreach ($parts as $value) {
			$path = '/' . $value;
			if ($path !== '/') {
				yield $path;
			}
		}
	}

	/**
	 * @return false|string
	 *
	 * Replace path
	 */
	public static function replacePath(string $path, string $segment) : false|string {
		$pos = strpos($path, $segment);
		if ($pos === false) {
			return false;
		}
		return substr($path, $pos + strlen($segment));
	}

	/**
	 * Replace advanced
	 */
	public static function replaceAdvanced(string $text, string $search, string $replace) : ?string {
		return preg_replace('/(?<!-)(' . $search . ')(?!d)/', $replace, $text);
	}

	public static function evenlyDivide(int $number, int $parts) : Generator {
		$quotient = intdiv($number, $parts);
		$remainder = $number % $parts;

		for ($i = 0; $i < $parts; $i++) {
			yield $quotient + ($remainder > 0 ? 1 : 0);
			$remainder--;
		}
	}

	/**
	 * @param array<int, mixed> $array
	 */
	public static function splitArray(array $array, int $size) : Generator {
		$totalItems = count($array);
		$quotient = intdiv($totalItems, $size);
		$remainder = $totalItems % $size;

		$offset = 0;
		for ($i = 0; $i < $size; $i++) {
			$length = $quotient + ($remainder > 0 ? 1 : 0);

			yield array_slice($array, $offset, $length);

			$offset += $length;
			$remainder--;
		}
	}

	/**
	 * @throws ReflectionException
	 */
	public static function isClass(string $class) : bool {
		$trace = debug_backtrace();
		if (isset($trace[2])) {
			if (!empty($trace[2]['args'])) {
				$args = $trace[2]['args'];
				/** @var Closure $closure */
				$closure = $args[0];
				$reflectionFunction = new ReflectionFunction($closure);
				$scopeClass = $reflectionFunction->getClosureScopeClass();
				if ($scopeClass === null) {
					return false;
				}
				return $scopeClass->getName() === $class;
			} else {
				return true; // This is a class
			}
		}
		return false;
	}

	/**
	 * @return string
	 *
	 * Get string after sign
	 */
	public static function getStringAfterSign(string $string, string $sign) : string {
		if (preg_match('/' . preg_quote($sign, '/') . '(.*)/s', $string, $matches)) {
			return $matches[1];
		}
		return '';
	}

	/**
	 * @return array<int|string, bool|string>
	 *
	 * Convert data to string
	 */
	public static function toStringAny(mixed $data) : array {
		$type = gettype($data);
		if (!is_callable($data) && (is_array($data) || is_object($data))) {
			return [$type => json_encode($data)];
		} elseif (is_bool($data)) {
			$data = $data ? 'true' : 'false';
			return [$type => $data];
		} elseif (is_resource($data)) {
			return [$type => get_resource_type($data)];
		} elseif (is_null($data)) {
			return [$type => 'null'];
		} elseif (is_callable($data)) {
			/** @phpstan-ignore-next-line */
			return ['callable' => self::closureToStringSafe($data)];
		} elseif (is_string($data)) {
			return [$type => '\'' . $data . '\''];
		}
		/** @phpstan-ignore-next-line */
		return [$type => (string) $data];
	}

	/**
	 * @param array<string, string> $data
	 * @return mixed
	 *
	 * Convert data to real it's type
	 */
	public static function fromStringToAny(array $data) : mixed {
		$type = array_keys($data)[0];
		$value = array_values($data)[0];
		return match ($type) {
			'boolean' => $value === 'true',
			'integer' => (int) $value,
			'float' => (float) $value,
			'double' => (float) $value,
			'string' => $value,
			'array' => json_decode($value, true),
			'object' => json_decode($value),
			'callable' => eval('return ' . $value . ';'),
			'null' => null,
			default => $value,
		};
	}
}
