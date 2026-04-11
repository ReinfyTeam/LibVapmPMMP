<?php

declare(strict_types=1);

use ReflectionClass;
use ReflectionMethod;

require_once __DIR__ . '\..\..\vendor\autoload.php';

$checks = [
	"pluginBaseClass" => class_exists(\pocketmine\plugin\PluginBase::class),
	"taskSchedulerClass" => class_exists(\pocketmine\scheduler\TaskScheduler::class),
];

$scheduleRepeatingTaskParams = -1;
$scheduleRepeatingTaskPublic = false;
if ($checks["taskSchedulerClass"]) {
	$method = new ReflectionMethod(\pocketmine\scheduler\TaskScheduler::class, "scheduleRepeatingTask");
	$scheduleRepeatingTaskParams = $method->getNumberOfParameters();
	$scheduleRepeatingTaskPublic = $method->isPublic();
}

$taskHandlerHasCancel = false;
if (class_exists(\pocketmine\scheduler\TaskHandler::class)) {
	$taskHandlerHasCancel = (new ReflectionClass(\pocketmine\scheduler\TaskHandler::class))->hasMethod("cancel");
}

echo json_encode([
	"type" => "pmmp-api5-compat",
	"checks" => $checks,
	"scheduleRepeatingTaskParamCount" => $scheduleRepeatingTaskParams,
	"scheduleRepeatingTaskIsPublic" => $scheduleRepeatingTaskPublic,
	"taskHandlerHasCancel" => $taskHandlerHasCancel,
], JSON_PRETTY_PRINT) . PHP_EOL;
