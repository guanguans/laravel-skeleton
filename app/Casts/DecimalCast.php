<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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
        return $value !== null ? Decimal::create($value) : null;
    }

    #[\Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        // Convert your application value to the database value
        return $value !== null ? (string) $value : null;
    }
}
