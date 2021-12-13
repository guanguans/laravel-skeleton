<?php

namespace App\Traits;

trait CreateStaticAble
{
    public static function create(...$parameters)
    {
        return new static(...$parameters);
    }
}
