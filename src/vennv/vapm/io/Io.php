<?php

declare(strict_types=1);

namespace vennv\vapm\io;

use vennv\vapm\Async;
use vennv\vapm\Promise;
use vennv\vapm\System;

final class Io
{
    public static function async(callable $callback): Async
    {
        return new Async($callback);
    }

    /**
     * @param mixed $await
     * @return mixed
     */
    public static function await($await)
    {
        return Async::await($await);
    }

    public static function delay(int $milliseconds): Promise
    {
        return new Promise(function ($resolve) use ($milliseconds): void {
            System::setTimeout(function () use ($resolve): void {
                $resolve();
            }, $milliseconds);
        });
    }

    public static function setTimeout(callable $callback, int $milliseconds): void
    {
        System::setTimeout($callback, $milliseconds);
    }

    public static function setInterval(callable $callback, int $milliseconds): void
    {
        System::setInterval($callback, $milliseconds);
    }
}
