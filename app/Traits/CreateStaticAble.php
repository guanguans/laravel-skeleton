<?php

namespace App\Traits;

trait CreateStaticable
{
    /**
     * @param ...$parameters
     * @return static
     */
    public static function create(...$parameters): static
    {
        return static::new(...$parameters);
    }

    /**
     * @param ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return static::new(...$parameters);
    }

    /**
     * @param ...$parameters
     * @return static
     */
    public static function new(...$parameters): static
    {
        return new static(...$parameters);
    }
}
