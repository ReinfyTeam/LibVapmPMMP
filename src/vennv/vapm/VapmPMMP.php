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

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

interface VapmPMMPInterface {
	/**
	 * @return void
	 *
	 * This function is used to initialize the VapmPMMP class.
	 * You should place this function in your onEnable() or onLoad() function.
	 */
	public static function init(PluginBase $plugin) : void;
}

final class VapmPMMP implements VapmPMMPInterface {
	private static bool $isInit = false;

	public static function init(PluginBase $plugin) : void {
		if (!self::$isInit) {
			self::$isInit = true;
			EventLoop::init();
			$plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(fn() => System::runEventLoop()), 1);
		}
	}
}