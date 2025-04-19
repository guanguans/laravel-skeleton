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
use Mtownsend\ReadTime\ReadTime;

/**
 * @mixin \Illuminate\Support\Str
 */
#[Mixin(Str::class)]
class StrMixin
{
    /**
     * @see https://github.com/symfony/polyfill-php83
     */
    public static function jsonValidate(): \Closure
    {
        return static fn (string $json, int $depth = 512, int $flags = 0): bool => json_validate($json, $depth, $flags);
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

    /**
     * @see https://github.com/dasundev/dasun.dev
     * @see https://github.com/mtownsend5512/read-time
     * @see https://github.com/vdhicts/read-time
     */
    public static function readTime(): callable
    {
        /**
         * @param list<string>|string $content
         */
        return static fn (
            array|string $content,
            bool $omitSeconds = true,
            bool $abbreviated = false,
            int $wordsPerMinute = 230
        ): string => (new ReadTime($content, $omitSeconds, $abbreviated, $wordsPerMinute))->get();
    }
}
