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

final readonly class CurrencyCast implements CastsAttributes
{
    /**
     * @throws \Throwable
     */
    public function __construct(private int $digits = 2)
    {
        throw_if(1 > $digits, \InvalidArgumentException::class, 'Digits should be a number larger than zero.');
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): ?float
    {
        return null !== $value
            ? round($value / (10 ** $this->digits), $this->digits)
            : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        return $value * (10 ** $this->digits);
    }
}
