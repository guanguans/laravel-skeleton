<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Macros;

use Illuminate\Support\Str;

/**
 * @mixin \Illuminate\Support\Str
 */
class StrMacro
{
    /**
     * @psalm-suppress UnusedFunctionCall
     *
     * @noinspection BadExceptionsProcessingInspection
     *
     * @see https://github.com/symfony/polyfill-php83
     */
    public static function jsonValidate(): \Closure
    {
        return static function (string $json, int $depth = 512, int $flags = 0): bool {
            if (0 !== $flags && \defined('JSON_INVALID_UTF8_IGNORE') && JSON_INVALID_UTF8_IGNORE !== $flags) {
                throw new \ValueError('json_validate(): Argument #3 ($flags) must be a valid flag (allowed flags: JSON_INVALID_UTF8_IGNORE)');
            }

            if ($depth <= 0) {
                throw new \ValueError('json_validate(): Argument #2 ($depth) must be greater than 0');
            }

            // see https://www.php.net/manual/en/function.json-decode.php
            if ($depth > ($jsonMaxDepth = 0x7FFFFFFF)) {
                throw new \ValueError(sprintf('json_validate(): Argument #2 ($depth) must be less than %d', $jsonMaxDepth));
            }

            json_decode($json, null, $depth, $flags);

            return JSON_ERROR_NONE === json_last_error();
        };
    }

    public static function appendIf(): callable
    {
        return static fn ($value, $suffix) => Str::endsWith($value, $suffix) ? $value : $value.$suffix;
    }

    public static function prependIf(): callable
    {
        return static fn ($value, $prefix) => Str::startsWith($value, $prefix) ? $value : $prefix.$value;
    }

    public static function mbSubstrCount(): callable
    {
        // return fn($haystack, $needle, $encoding = null) => mb_substr_count($haystack, $needle, $encoding);

        return static fn ($haystack, $needle, $encoding = null): int => mb_substr_count($haystack, $needle, $encoding);
    }

    public static function pipe(): callable
    {
        return static fn ($value, callable $callback) => $callback($value);
    }

    /**
     * @see https://github.com/koenhendriks/laravel-str-acronym
     */
    public static function acronym(): callable
    {
        return static function ($string, $delimiter = ''): string {
            if (empty($string)) {
                return '';
            }

            $acronym = '';
            foreach (preg_split('/[^\p{L}]+/u', $string) as $word) {
                if (! empty($word)) {
                    $firstLetter = mb_substr($word, 0, 1);
                    $acronym .= $firstLetter.$delimiter;
                }
            }

            return $acronym;
        };
    }
}
