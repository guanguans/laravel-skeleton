<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
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
