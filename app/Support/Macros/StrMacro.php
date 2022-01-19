<?php

namespace App\Support\Macros;

use Illuminate\Support\Str;

class StrMacro
{
    public static function appendIf(): callable
    {
        return function ($value, $suffix) {
            return Str::endsWith($value, $suffix) ? $value : $value . $suffix;
        };
    }

    public static function prependIf(): callable
    {
        return function ($value, $prefix) {
            return  Str::startsWith($value, $prefix) ? $value : $prefix . $value;
        };
    }
}
