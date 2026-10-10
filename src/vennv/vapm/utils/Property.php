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

namespace vennv\vapm\utils;

use Exception;
use function property_exists;

// This is trait for JsonData|StaticData class
/**
 * @phpstan-ignore-next-line trait.unused
 */
trait Property {
	/**
	 * @param array<string, mixed> $options
	 * @throws Exception
	 */
	public function update(object $data, array $options) : object {
		/**
		 * @var string $key
		 * @var mixed $value
		 */
		foreach ($options as $key => $value) {
			if (property_exists($data, $key)) {
				$data->{$key} = $value;
			}
			/* @phpstan-ignore-line */
		}

		return $data;
	}
}
