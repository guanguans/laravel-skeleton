<?php

namespace App\Traits;

trait CreateStaticable
{
    /**
     * @param ...$parameters
     * @return static
     */
    public static function create(...$parameters)
    {
        return new static(...$parameters);
    }

    /**
     * @param ...$parameters
     * @return static
     */
    public static function make(...$parameters)
    {
        return static::create(...$parameters);
    }
}
