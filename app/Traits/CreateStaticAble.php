<?php

declare(strict_types=1);

namespace App\Traits;

trait CreateStaticable
{
    public static function create(...$parameters): static
    {
        return static::new(...$parameters);
    }

    public static function make(...$parameters): static
    {
        return static::new(...$parameters);
    }

    public static function new(...$parameters): static
    {
        return new static(...$parameters);
    }
}
