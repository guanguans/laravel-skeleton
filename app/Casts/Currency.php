<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Currency implements CastsAttributes
{
    /**
     * The amount of digits.
     *
     * @var int
     */
    protected $digits;

    /**
     * Constructor
     *
     * @param  int $digits The amount of digits to handle.
     * @return void
     *
     * @throws \InvalidArgumentException Thrown on invalid input.
     */
    public function __construct(int $digits = 2)
    {
        if ($digits < 1) {
            throw new \InvalidArgumentException('Digits should be a number larger than zero.');
        }

        $this->digits = $digits;
    }

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model      The model object.
     * @param  string                              $key        The property name.
     * @param  mixed                               $value      The property value.
     * @param  array                               $attributes The model attributes array.
     * @return float
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $value !== null
            ? round($value / (10 ** $this->digits), $this->digits)
            : null;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model      The model object.
     * @param  string                              $key        The property name.
     * @param  mixed                               $value      The property value.
     * @param  array                               $attributes The model attributes array.
     * @return array
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return (int) ($value * (10 ** $this->digits));
    }
}
