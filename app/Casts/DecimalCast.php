<?php

declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use PhpCollective\DecimalObject\Decimal;

class DecimalCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Decimal
    {
        // Convert the database value to your application value
        return $value !== null ? Decimal::create($value) : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        // Convert your application value to the database value
        return $value !== null ? (string) $value : null;
    }
}
