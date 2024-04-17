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

/**
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
