# LibVapmPMMP

Async/Promise/Coroutine/Thread utilities for PocketMine-MP (virion-ready).

## Requirements
- PMMP API: `5.0.0`
- PHP: `8.1 - 8.4`

## Installation

### Composer
```bash
composer require reinfyteam/libvapm-pmmp
```

### Virion
- Download from Poggit: https://poggit.pmmp.io/ci/VennDev/ReinfyTeam/LibVapmPMMP
- Place the virion in your project virions folder.

## Quick setup

Initialize once in your plugin:

```php
use pocketmine\plugin\PluginBase;
use vennv\vapm\VapmPMMP;

final class Main extends PluginBase{
    protected function onEnable() : void{
        VapmPMMP::init($this);
    }
}
```

`VapmPMMP::init()` registers the repeating event-loop tick task.

## Recommended imports

```php
use vennv\vapm\VapmPMMP;
use vennv\vapm\io\Io;
use vennv\vapm\ct\Ct;
use vennv\vapm\Promise;
use vennv\vapm\System;
```

## Usage

### 1) Async + await (Io facade)

```php
$job = Io::async(function () : string {
    return "done";
});

$result = Io::await($job); // "done"
```

### 2) Delay / timers

```php
Io::setTimeout(function () : void {
    // run once after delay
}, 50);

Io::setInterval(function () : void {
    // run repeatedly
}, 20);

Io::delay(100)->then(function () : void {
    // delayed promise flow
});
```

### 3) Coroutines (Ct facade)

```php
use Generator;

Ct::c(function () : Generator {
    yield from Ct::cDelay(10);
    // coroutine work
});

Ct::cBlock(function () : Generator {
    yield from Ct::cDelay(5);
    // blocking coroutine run
});
```

### 4) Promise composition

```php
$all = Promise::all([
    Io::async(fn() => 1),
    Io::async(fn() => 2),
]);

$values = Io::await($all); // [1, 2]
```

Also available: `Promise::allSettled()`, `Promise::any()`, `Promise::race()`.

### 5) HTTP/file helpers (System)

```php
System::fetch("https://example.com")->then(function ($response) : void {
    // InternetRequestResult
});

System::read("/path/to/file.txt")->then(function (string $content) : void {
    // file contents
});
```

## Notes for high-throughput workloads
- Keep long operations async/coroutine-based.
- Prefer batching and composition (`Promise::all`, channels, await groups) over deep nested callbacks.
- For optimization roadmap and upcoming improvements, see [`WATCHLIST.md`](WATCHLIST.md).