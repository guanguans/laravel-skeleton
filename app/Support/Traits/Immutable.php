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
