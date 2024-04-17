<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Traits;

trait CreateStaticable
{
    public static function create(...$parameters): static
    {
        return static::new(...$parameters);
    }

    public static function make(...$parameters): static
    {
        return static::new(...$parameters);
    }

    /**
     * @noinspection PhpMethodParametersCountMismatchInspection
     */
    public static function new(...$parameters): static
    {
        return new static(...$parameters);
    }
}
