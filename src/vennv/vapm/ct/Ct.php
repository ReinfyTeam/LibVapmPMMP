<?php

declare(strict_types=1);

namespace vennv\vapm\ct;

use Closure;
use Generator;
use vennv\vapm\AwaitGroup;
use vennv\vapm\Channel;
use vennv\vapm\CoroutineGen;
use vennv\vapm\Deferred;
use vennv\vapm\Mutex;

final class Ct
{
    public static function c(callable ...$callbacks): void
    {
        CoroutineGen::runNonBlocking(...$callbacks);
    }

    public static function cBlock(callable ...$callbacks): void
    {
        CoroutineGen::runBlocking(...$callbacks);
    }

    public static function cDelay(int $milliseconds): Generator
    {
        return CoroutineGen::delay($milliseconds);
    }

    public static function cRepeat(callable $callback, int $times): Closure
    {
        return CoroutineGen::repeat($callback, $times);
    }

    public static function channel(): Channel
    {
        return new Channel();
    }

    public static function awaitGroup(): AwaitGroup
    {
        return new AwaitGroup();
    }

    public static function mutex(): Mutex
    {
        return new Mutex();
    }

    public static function deferred(callable $callback): Deferred
    {
        return new Deferred($callback);
    }
}
