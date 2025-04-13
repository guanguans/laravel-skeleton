<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Support\Str;

/**
 * @mixin \Illuminate\Support\Str
 */
#[Mixin(Str::class)]
class StrMixin
{
    /**
     * @noinspection BadExceptionsProcessingInspection
     *
     * @see https://github.com/symfony/polyfill-php83
     */
    public static function jsonValidate(): \Closure
    {
        return static function (string $json, int $depth = 512, int $flags = 0): bool {
            throw_if(0 !== $flags && \defined('JSON_INVALID_UTF8_IGNORE') && \JSON_INVALID_UTF8_IGNORE !== $flags, \ValueError::class, 'json_validate(): Argument #3 ($flags) must be a valid flag (allowed flags: JSON_INVALID_UTF8_IGNORE)');

            throw_if(0 >= $depth, \ValueError::class, 'json_validate(): Argument #2 ($depth) must be greater than 0');

            // see https://www.php.net/manual/en/function.json-decode.php
            if ($depth > ($jsonMaxDepth = 0x7FFFFFFF)) {
                throw new \ValueError(\sprintf('json_validate(): Argument #2 ($depth) must be less than %d', $jsonMaxDepth));
            }

            json_decode($json, null, $depth, $flags);

            return \JSON_ERROR_NONE === json_last_error();
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
        return static function ($string, string $delimiter = ''): string {
            if (empty($string)) {
                return '';
            }

            $acronym = '';

            foreach (preg_split('/[^\p{L}]+/u', $string) as $word) {
                if (!empty($word)) {
                    $firstLetter = mb_substr($word, 0, 1);
                    $acronym .= $firstLetter.$delimiter;
                }
            }

            return $acronym;
        };
    }
}
