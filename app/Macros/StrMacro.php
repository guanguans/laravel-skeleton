<?php

declare(strict_types=1);

namespace App\Macros;

use Illuminate\Support\Str;

/**
 * @mixin \Illuminate\Support\Str
 */
class StrMacro
{
    public static function appendIf(): callable
    {
        return fn ($value, $suffix) => Str::endsWith($value, $suffix) ? $value : $value.$suffix;
    }

    public static function prependIf(): callable
    {
        return fn ($value, $prefix) => Str::startsWith($value, $prefix) ? $value : $prefix.$value;
    }

    public static function mbSubstrCount(): callable
    {
        // return fn($haystack, $needle, $encoding = null) => mb_substr_count($haystack, $needle, $encoding);

        return fn ($haystack, $needle, $encoding = null) => mb_substr_count($haystack, $needle, $encoding);
    }

    public static function pipe(): callable
    {
        return fn ($value, callable $callback) => $callback($value);
    }

    /**
     * @see https://github.com/koenhendriks/laravel-str-acronym
     */
    public static function acronym(): callable
    {
        return function ($string, $delimiter = '') {
            if (empty($string)) {
                return '';
            }

            $acronym = '';
            foreach (preg_split('/[^\p{L}]+/u', $string) as $word) {
                if (! empty($word)) {
                    $first_letter = mb_substr($word, 0, 1);
                    $acronym .= $first_letter.$delimiter;
                }
            }

            return $acronym;
        };
    }
}
