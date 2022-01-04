<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Base64Cast implements CastsAttributes
{
    /**
     * @var bool
     */
    private $isCastGet;

    /**
     * @var bool
     */
    private $isCastSet;

    /**
     * @param  bool  $isCastGet
     * @param  bool  $isCastSet
     */
    public function __construct(bool $isCastGet = true, bool $isCastSet = false)
    {
        $this->isCastGet = $isCastGet;
        $this->isCastSet = $isCastSet;
    }

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     *
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
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     *
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $this->isCastSet ? base64_decode($value) : $value;
    }
}
