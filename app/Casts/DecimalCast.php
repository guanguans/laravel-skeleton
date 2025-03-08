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

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use PhpCollective\DecimalObject\Decimal;

class DecimalCast implements CastsAttributes
{
    #[\Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Decimal
    {
        // Convert the database value to your application value
        return null !== $value ? Decimal::create($value) : null;
    }

    #[\Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        // Convert your application value to the database value
        return null !== $value ? (string) $value : null;
    }
}
