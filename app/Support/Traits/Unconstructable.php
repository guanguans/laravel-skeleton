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
 * The **unconstructable** trait can be used to disable the ability to construct
 * an instance with the `new` keyword. It is specifically useful to create
 * final abstract classes and thus avoiding problems with non-final classes in
 * libraries that wish to adhere to SemVer semantics.
 *
 * The method is _final_ and _protected_ to avoid that subclasses overwrite it
 * and circumvent the restrictions laid out by the superclass.
 */
trait Unconstructable
{
    final protected function __construct() {}
}
