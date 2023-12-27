<?php

namespace App\Rules;

class EvenNumberRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^\d*[02468]$/';
    }
}
