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

    public static function mbSubstrCount(): callable
    {
        // return fn($haystack, $needle, $encoding = null) => mb_substr_count($haystack, $needle, $encoding);

        return function ($haystack, $needle, $encoding = null) {
            return mb_substr_count($haystack, $needle, $encoding);
        };
    }
}
