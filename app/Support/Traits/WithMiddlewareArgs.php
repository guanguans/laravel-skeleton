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
 * @see \Guanguans\LaravelApiResponse\Support\Traits\WithPipeArgs
 * @see \Guanguans\LaravelExceptionNotify\Support\Traits\WithPipeArgs
 * @see \TiMacDonald\Middleware\HasParameters
 */
trait WithMiddlewareArgs
{
    public static function with(mixed ...$args): string
    {
        return [] === $args ? static::class : static::class.':'.implode(',', $args);
    }
}
