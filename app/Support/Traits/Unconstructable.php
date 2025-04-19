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
 * @see https://github.com/coralsio/laraship/blob/main/Corals/core/Foundation/Formatter/Unconstructable.php
 *
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
