<?php

declare(strict_types=1);

use vennv\vapm\EventLoop;
use vennv\vapm\System;
use vennv\vapm\Async;

require_once __DIR__ . '/../vendor/autoload.php';

$total = max(1, (int) ($argv[1] ?? 5000));
$jobs = [];

for ($i = 1; $i <= $total; $i++) {
	$jobs[] = new Async(static fn() : int => $i);
}

System::runSingleEventLoop();

$sum = 0;
foreach ($jobs as $job) {
	$promise = EventLoop::getReturn($job->getId());
	if ($promise !== null) {
		$sum += (int) $promise->getResult();
	}
}

echo json_encode([
	"type" => "stress-queue",
	"jobs" => $total,
	"sum" => $sum,
], JSON_PRETTY_PRINT) . PHP_EOL;
