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

namespace vennv\vapm;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use function register_shutdown_function;
use function spl_object_id;
use function trigger_error;
use const E_USER_WARNING;

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

	private static int $pluginId = -1;

	public static function init(PluginBase $plugin) : void {
		$pluginId = spl_object_id($plugin);

		if (self::$isInit) {
			if (self::$pluginId !== $pluginId) {
				trigger_error(Error::PMMP_DOUBLE_INIT_GUARD, E_USER_WARNING);
			}
			return;
		}

		if (!$plugin->isEnabled()) {
			trigger_error(Error::PMMP_PLUGIN_NOT_ENABLED, E_USER_WARNING);
			return;
		}

		self::$isInit = true;
		self::$pluginId = $pluginId;
		System::setPmmpManaged(true);
		EventLoop::init();
		System::init();
		register_shutdown_function(fn() => System::runSingleEventLoop());
		$plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(fn() => System::runEventLoop()), 1);
	}
}
