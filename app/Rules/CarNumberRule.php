<?php

namespace App\Rules;

class CarNumberRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/';
    }
}
