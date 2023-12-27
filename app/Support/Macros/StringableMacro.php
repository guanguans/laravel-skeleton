<?php

declare(strict_types=1);

namespace App\Support\Macros;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

/**
 * @mixin \Illuminate\Support\Stringable
 */
class StringableMacro
{
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
