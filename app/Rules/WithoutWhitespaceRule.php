<?php

namespace App\Rules;

final class WithoutWhitespaceRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/\s/';
    }
}
