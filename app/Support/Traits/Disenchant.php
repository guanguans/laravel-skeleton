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
 * @see https://github.com/coralsio/laraship/blob/main/Corals/core/Foundation/Formatter/Disenchant.php
 *
 * The **disenchant** trait can be used to disable dynamic property interactions
 * of objects.
 *
 * The magic methods defined here must be _public_, due to PHP aborting with
 * a fatal error if they are not. This means in effect that throwing of an error
 * is the only thing that is possible to disable dynamic property interactions.
 *
 * The methods must be _final_ too, in order to avoid that subclasses override
 * them, and consequently circumvent the restrictions laid out by the
 * superclass.
 */
trait Disenchant
{
    /**
     * @throws \Error
     */
    final public function __get(mixed $_): void
    {
        throw new \Error('Cannot get dynamic properties from immutable class '.static::class);
    }

    /**
     * @throws \Error
     */
    final public function __isset(mixed $_): bool
    {
        throw new \Error('Cannot check if dynamic properties are set on immutable class '.static::class);
    }

    /**
     * @throws \Error
     */
    final public function __set(mixed $_, mixed $__): void
    {
        throw new \Error('Cannot set dynamic properties on immutable class '.static::class);
    }

    /**
     * @throws \Error
     */
    final public function __unset(mixed $_): void
    {
        throw new \Error('Cannot remove dynamic properties from immutable class '.static::class);
    }
}
