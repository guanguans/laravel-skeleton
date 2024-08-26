<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\ApiResponse\Pipes\Concerns;

trait WithArgs
{
    public static function with(...$args): string
    {
        if ($args === []) {
            return static::class;
        }

        return static::class.':'.implode(',', $args);
    }
}
