<?php

namespace App\Rules;

final class WithoutWhitespaceRule extends RegexRule
{
    protected function pattern(): string
    {
        return '/\s/';
    }
}
