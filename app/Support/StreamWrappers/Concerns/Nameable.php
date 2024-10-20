<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\StreamWrappers\Concerns;

/**
 * @mixin \App\Support\StreamWrappers\StreamWrapper
 */
trait Nameable
{
    final public static function name(): string
    {
        return str(static::class)
            ->classBasename()
            ->beforeLast('StreamWrapper')
            ->snake('-')
            ->toString();
    }
}
