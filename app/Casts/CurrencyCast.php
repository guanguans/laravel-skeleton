<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<?float, int>
 */
final readonly class CurrencyCast implements CastsAttributes
{
    /**
     * @param int<0, 8> $digits
     */
    public function __construct(private int $digits = 2)
    {
        if (1 > $digits) {
            throw new \InvalidArgumentException('Digits should be a number larger than zero.');
        }
    }

    #[\Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): ?float
    {
        return null !== $value ? round($value / (10 ** $this->digits), $this->digits) : null;
    }

    #[\Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        return (int) $value * (10 ** $this->digits);
    }
}
