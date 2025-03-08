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
use Illuminate\Support\Stringable;

/**
 * @mixin \Illuminate\Support\Stringable
 */
#[Mixin(Stringable::class)]
class StringableMixin
{
    /**
     * @psalm-suppress InaccessibleProperty
     */
    public function jsonValidate(): \Closure
    {
        return fn (int $depth = 512, int $flags = 0): bool => Str::jsonValidate($this->value, $depth, $flags);
    }

    public function appendIf(): callable
    {
        return fn ($suffix): Stringable => new Stringable(
            Str::appendIf($this->__toString(), $suffix)
        );
    }

    public function prependIf(): callable
    {
        return fn ($prefix): Stringable => new Stringable(
            Str::prependIf($this->__toString(), $prefix)
        );
    }

    public function mbSubstrCount(): callable
    {
        return fn ($needle, $encoding = null): Stringable => new Stringable(
            Str::mbSubstrCount($this->__toString(), $needle, $encoding)
        );
    }

    public function get(): callable
    {
        return fn () => $this->__toString();
    }

    /**
     * @see https://github.com/koenhendriks/laravel-str-acronym
     */
    public function acronym(): callable
    {
        return fn (string $delimiter = ''): Stringable => new Stringable(
            Str::acronym($this->value, $delimiter)
        );
    }
}
