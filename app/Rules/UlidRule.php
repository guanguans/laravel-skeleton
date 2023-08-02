<?php

namespace App\Rules;

final class UlidRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/[0-7][0-9A-HJKMNP-TV-Z]{25}/';
    }
}
