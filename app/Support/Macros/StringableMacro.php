<?php

namespace App\Support\Macros;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class StringableMacro
{
    public function appendIf(): callable
    {
        return function ($suffix) {
            /** @var Stringable $this */
            return new Stringable(
                Str::appendIf($this->__toString(), $suffix)
            );
        };
    }

    public function prependIf(): callable
    {
        return function ($prefix) {
            /** @var Stringable $this */
            return new Stringable(
                Str::prependIf($this->__toString(), $prefix)
            );
        };
    }

    public function mbSubstrCount(): callable
    {
        return function ($needle, $encoding = null) {
            /** @var Stringable $this */
            return new Stringable(
                Str::mbSubstrCount($this->__toString(), $needle, $encoding)
            );
        };
    }
}
