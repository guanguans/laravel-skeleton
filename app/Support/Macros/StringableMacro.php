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
use Illuminate\Support\Stringable;

/**
 * @mixin \Illuminate\Support\Stringable
 */
class StringableMacro
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
        return fn ($suffix) => new Stringable(
            Str::appendIf($this->__toString(), $suffix)
        );
    }

    public function prependIf(): callable
    {
        return fn ($prefix) => new Stringable(
            Str::prependIf($this->__toString(), $prefix)
        );
    }

    public function mbSubstrCount(): callable
    {
        return fn ($needle, $encoding = null) => new Stringable(
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
        return fn (string $delimiter = '') => new Stringable(
            Str::acronym($this->value, $delimiter)
        );
    }
}
