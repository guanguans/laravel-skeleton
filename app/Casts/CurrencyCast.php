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

class CurrencyCast implements CastsAttributes
{
    /**
     * @param  int  $digits  the amount of digits to handle
     * @return void
     *
     * @throws \InvalidArgumentException thrown on invalid input
     */
    public function __construct(/**
     * The amount of digits.
     */
        protected int $digits = 2
    ) {
        throw_if($digits < 1, \InvalidArgumentException::class, 'Digits should be a number larger than zero.');
    }

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model  the model object
     * @param  string  $key  the property name
     * @param  mixed  $value  the property value
     * @param  array  $attributes  the model attributes array
     */
    #[\Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): float
    {
        return $value !== null
            ? round($value / (10 ** $this->digits), $this->digits)
            : null;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model  the model object
     * @param  string  $key  the property name
     * @param  mixed  $value  the property value
     * @param  array  $attributes  the model attributes array
     */
    #[\Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        return $value * (10 ** $this->digits);
    }
}
