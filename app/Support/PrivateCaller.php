<?php

/** @noinspection PhpExpressionAlwaysNullInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support;

/**
 * @see https://github.com/spatie/invade
 */
class PrivateCaller
{
    public static function getStatic(string $class, string $property): mixed
    {
        return (static fn (): mixed => static::${$property})->bindTo(null, $class)();
    }

    public static function setStatic(string $class, string $property, mixed $value): void
    {
        (static fn (): mixed => static::${$property} = $value)->bindTo(null, $class)();
    }

    public static function callStatic(string $class, string $method, array $params = []): mixed
    {
        return (static fn (): mixed => static::{$method}(...$params))->bindTo(null, $class)();
    }

    public static function get(object $object, string $property): mixed
    {
        return (fn (): mixed => $this->{$property})->call($object);
    }

    public static function set(object $object, string $property, mixed $value): void
    {
        (fn (): mixed => $this->{$property} = $value)->call($object);
    }

    public static function call(object $object, string $method, array $params = []): mixed
    {
        return (fn (): mixed => $this->{$method}(...$params))->call($object);
    }
}
