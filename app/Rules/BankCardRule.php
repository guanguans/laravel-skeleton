<?php

namespace App\Rules;

final class BankCardRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^[1-9]\d{9,29}$/';
    }
}
