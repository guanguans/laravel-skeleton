<?php

/** @noinspection PhpUndefinedMethodInspection */

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
use Illuminate\Support\Stringable;

/**
 * @mixin \Illuminate\Support\Stringable
 */
#[Mixin(Stringable::class)]
final class StringableMixin
{
    public function jsonValidate(): \Closure
    {
        return fn (int $depth = 512, int $flags = 0): bool => Str::jsonValidate($this->value, $depth, $flags);
    }

    public function appendIf(): \Closure
    {
        return fn (string $suffix): Stringable => new Stringable(
            Str::appendIf((string) $this, $suffix)
        );
    }

    public function prependIf(): \Closure
    {
        return fn (string $prefix): Stringable => new Stringable(
            Str::prependIf((string) $this, $prefix)
        );
    }

    public function mbSubstrCount(): \Closure
    {
        return fn (string $needle, ?string $encoding = null): Stringable => new Stringable(
            Str::mbSubstrCount((string) $this, $needle, $encoding)
        );
    }

    public function get(): \Closure
    {
        return fn () => (string) $this;
    }

    public function acronym(): \Closure
    {
        return fn (string $delimiter = ''): Stringable => new Stringable(
            Str::acronym($this->value, $delimiter)
        );
    }

    public function readTime(): \Closure
    {
        return fn (
            bool $omitSeconds = true,
            bool $abbreviated = false,
            int $wordsPerMinute = 230
        ): Stringable => new Stringable(
            Str::readTime($this->value, $omitSeconds, $abbreviated, $wordsPerMinute)
        );
    }
}
