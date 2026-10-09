<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$manifest = yaml_parse_file(__DIR__ . '/../plugin.yml');
if (!is_array($manifest) || !str_starts_with((string) ($manifest['api'] ?? ''), '5.')) {
	throw new RuntimeException('plugin.yml must target PocketMine-MP API 5.');
}

if (!is_subclass_of(vennv\vapm\Loader::class, pocketmine\plugin\PluginBase::class)) {
	throw new RuntimeException('The plugin loader must extend PocketMine-MP PluginBase.');
}

echo 'PocketMine-MP API 5 compatibility structure passed.' . PHP_EOL;
