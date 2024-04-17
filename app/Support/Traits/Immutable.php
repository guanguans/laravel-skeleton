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
 * The **immutable** trait is a combination of the {@see Disenchant},
 * {@see Uncloneable}, and {@see Unconstructable} traits. It is the perfect
 * foundation for immutable objects and disables all functionality that could
 * be misused to mutate the state of an object.
 *
 * Obviously mutation is always possible through reflection, this cannot and
 * should not be disabled.
 */
trait Immutable
{
    use Disenchant;
    use Uncloneable;
    use Unconstructable;
}
