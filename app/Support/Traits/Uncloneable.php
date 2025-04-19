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
 * @see https://github.com/coralsio/laraship/blob/main/Corals/core/Foundation/Formatter/Uncloneable.php
 *
 * The **uncloneable** trait can be used to disable the ability of cloning of
 * objects with the `clone` keyword. It is specifically useful to create
 * immutable classes where cloning makes no sense.
 *
 * The method is _final_ and _protected_ to avoid that subclasses overwrite it
 * and circumvent the restrictions laid out by the superclass.
 */
trait Uncloneable
{
    final protected function __clone() {}
}
