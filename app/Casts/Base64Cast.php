<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Base64Cast implements CastsAttributes
{
    public function __construct(
        private readonly bool $isCastGet = true,
        private readonly bool $isCastSet = false
    ) {}

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $this->isCastGet ? base64_encode($value) : $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $this->isCastSet ? base64_decode($value) : $value;
    }
}
