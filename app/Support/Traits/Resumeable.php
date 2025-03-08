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
 * @see https://dev.to/lotyp/laravel-config-problem-is-it-time-for-a-revolution-159f
 * @see https://github.com/lotyp
 * @see https://github.com/wayofdev
 * @see https://x.com/wlotyp
 */
trait Resumeable
{
    public static function __set_state(array $properties): static
    {
        $object = new static;

        foreach ($properties as $property => $value) {
            if (!property_exists($object, $property)) {
                continue;
            }

            $object->{$property} = $value;
        }

        return $object;
    }
}
