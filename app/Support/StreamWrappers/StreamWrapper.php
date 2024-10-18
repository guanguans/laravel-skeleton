<?php

declare(strict_types=1);

namespace App\Support\StreamWrappers;

/**
 * @see https://www.php.net/manual/zh/class.streamwrapper.php
 * @see https://www.php.net/manual/zh/stream.streamwrapper.example-1.php
 * @see https://www.php.net/manual/zh/wrappers.php
 * @see \GuzzleHttp\Psr7\StreamWrapper
 */
abstract class StreamWrapper
{
    public static function register(): void
    {
        if (! static::isRegistered()) {
            stream_wrapper_register(static::name(), static::class);
        }
    }

    public static function unregister(): void
    {
        if (static::isRegistered()) {
            stream_wrapper_unregister(static::name());
        }
    }

    public static function isRegistered(): bool
    {
        return \in_array(static::name(), stream_get_wrappers(), true);
    }

    abstract public static function name(): string;
}
