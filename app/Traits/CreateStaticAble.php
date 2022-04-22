<?php

namespace App\Traits;

trait CreateStaticable
{
    public static function create(...$parameters)
    {
        return new static(...$parameters);
    }
}
