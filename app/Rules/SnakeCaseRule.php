<?php

namespace App\Rules;

final class SnakeCaseRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^(?:\p{Ll}+_)*\p{Ll}+$/u';
    }
}
