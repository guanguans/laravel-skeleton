<?php

namespace App\Macros;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

/**
 * @mixin \Illuminate\Support\Stringable
 */
class StringableMacro
{
    public function appendIf(): callable
    {
        return function ($suffix) {
            return new Stringable(
                Str::appendIf($this->__toString(), $suffix)
            );
        };
    }

    public function prependIf(): callable
    {
        return function ($prefix) {
            return new Stringable(
                Str::prependIf($this->__toString(), $prefix)
            );
        };
    }

    public function mbSubstrCount(): callable
    {
        return function ($needle, $encoding = null) {
            return new Stringable(
                Str::mbSubstrCount($this->__toString(), $needle, $encoding)
            );
        };
    }

    public function get(): callable
    {
        return function () {
            return $this->__toString();
        };
    }

    /**
     * @see https://github.com/koenhendriks/laravel-str-acronym
     */
    public function acronym(): callable
    {
        return function (string $delimiter = '') {
            return new Stringable(
                Str::acronym($this->value, $delimiter)
            );
        };
    }
}
