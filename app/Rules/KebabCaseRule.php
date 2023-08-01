<?php

namespace App\Rules;

final class KebabCaseRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^(?:\p{Ll}+\-)*\p{Ll}+$/u';
    }
}
