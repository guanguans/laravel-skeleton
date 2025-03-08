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

class CurrencyCast implements CastsAttributes
{
    /**
     * @param int $digits the amount of digits to handle
     *
     * @throws \InvalidArgumentException thrown on invalid input
     */
    public function __construct(/**
     * The amount of digits.
     */
        protected int $digits = 2
    ) {
        throw_if(1 > $digits, \InvalidArgumentException::class, 'Digits should be a number larger than zero.');
    }

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param \Illuminate\Database\Eloquent\Model $model the model object
     * @param string $key the property name
     * @param mixed $value the property value
     * @param array $attributes the model attributes array
     */
    #[\Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): float
    {
        return null !== $value
            ? round($value / (10 ** $this->digits), $this->digits)
            : null;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param \Illuminate\Database\Eloquent\Model $model the model object
     * @param string $key the property name
     * @param mixed $value the property value
     * @param array $attributes the model attributes array
     */
    #[\Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        return $value * (10 ** $this->digits);
    }
}
