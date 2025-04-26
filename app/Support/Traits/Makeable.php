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

/**
 * @see \DragonCode\Support\Concerns\Makeable
 */
trait Makeable
{
    /**
     * @noinspection PhpMethodParametersCountMismatchInspection
     */
    public static function make(mixed ...$parameters): static
    {
        return new static(...$parameters);
    }
}
