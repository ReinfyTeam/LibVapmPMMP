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

use function max;

final class Settings {
	/**
	 * The time in seconds to check should drop the promise if the promise is not resolved or rejected
	 * in the specified time.
	 */
	public const TIME_DROP = 7;

	private static int $eventLoopBaseLimit = 256;

	private static int $eventLoopMaxLimit = 8192;

	private static int $gcSweepSize = 2048;

	private static int $gcSweepInterval = 8;

	private static int $macroTaskBaseLimit = 128;

	private static int $macroTaskMaxLimit = 2048;

	private static int $coroutineBaseLimit = 64;

	private static int $coroutineMaxLimit = 4096;

	private static int $workDrainChunkSize = 256;

	private static int $workerProducerChunkSize = 128;

	private static bool $debugScheduler = false;

	private static int $debugLogIntervalTicks = 20;

	private static int $healthWarnIntervalTicks = 20;

	private static int $healthWarnBacklogThreshold = 10000;

	private static int $healthWarnDropThreshold = 100;

	public static function getEventLoopBaseLimit() : int {
		return self::$eventLoopBaseLimit;
	}

	public static function getEventLoopMaxLimit() : int {
		return self::$eventLoopMaxLimit;
	}

	public static function setEventLoopLimits(int $baseLimit, int $maxLimit) : void {
		self::$eventLoopBaseLimit = max(1, $baseLimit);
		self::$eventLoopMaxLimit = max(self::$eventLoopBaseLimit, $maxLimit);
	}

	public static function getGcSweepSize() : int {
		return self::$gcSweepSize;
	}

	public static function getGcSweepInterval() : int {
		return self::$gcSweepInterval;
	}

	public static function setGarbageCollector(int $sweepSize, int $sweepInterval) : void {
		self::$gcSweepSize = max(1, $sweepSize);
		self::$gcSweepInterval = max(1, $sweepInterval);
	}

	public static function getMacroTaskBaseLimit() : int {
		return self::$macroTaskBaseLimit;
	}

	public static function getMacroTaskMaxLimit() : int {
		return self::$macroTaskMaxLimit;
	}

	public static function setMacroTaskLimits(int $baseLimit, int $maxLimit) : void {
		self::$macroTaskBaseLimit = max(1, $baseLimit);
		self::$macroTaskMaxLimit = max(self::$macroTaskBaseLimit, $maxLimit);
	}

	public static function getCoroutineBaseLimit() : int {
		return self::$coroutineBaseLimit;
	}

	public static function getCoroutineMaxLimit() : int {
		return self::$coroutineMaxLimit;
	}

	public static function setCoroutineLimits(int $baseLimit, int $maxLimit) : void {
		self::$coroutineBaseLimit = max(1, $baseLimit);
		self::$coroutineMaxLimit = max(self::$coroutineBaseLimit, $maxLimit);
	}

	public static function getWorkDrainChunkSize() : int {
		return self::$workDrainChunkSize;
	}

	public static function setWorkDrainChunkSize(int $chunkSize) : void {
		self::$workDrainChunkSize = max(1, $chunkSize);
	}

	public static function getWorkerProducerChunkSize() : int {
		return self::$workerProducerChunkSize;
	}

	public static function setWorkerProducerChunkSize(int $chunkSize) : void {
		self::$workerProducerChunkSize = max(1, $chunkSize);
	}

	public static function isSchedulerDebugEnabled() : bool {
		return self::$debugScheduler;
	}

	public static function setSchedulerDebug(bool $enabled) : void {
		self::$debugScheduler = $enabled;
	}

	public static function getDebugLogIntervalTicks() : int {
		return self::$debugLogIntervalTicks;
	}

	public static function setDebugLogIntervalTicks(int $interval) : void {
		self::$debugLogIntervalTicks = max(1, $interval);
	}

	public static function getHealthWarnIntervalTicks() : int {
		return self::$healthWarnIntervalTicks;
	}

	public static function setHealthWarnIntervalTicks(int $interval) : void {
		self::$healthWarnIntervalTicks = max(1, $interval);
	}

	public static function getHealthWarnBacklogThreshold() : int {
		return self::$healthWarnBacklogThreshold;
	}

	public static function setHealthWarnBacklogThreshold(int $threshold) : void {
		self::$healthWarnBacklogThreshold = max(1, $threshold);
	}

	public static function getHealthWarnDropThreshold() : int {
		return self::$healthWarnDropThreshold;
	}

	public static function setHealthWarnDropThreshold(int $threshold) : void {
		self::$healthWarnDropThreshold = max(1, $threshold);
	}
}
