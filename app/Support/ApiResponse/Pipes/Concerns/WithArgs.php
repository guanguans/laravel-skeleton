<?php

declare(strict_types=1);

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
