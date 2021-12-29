<?php

namespace App\Rules;

final class BankCardRule extends RegexRule
{
    protected function pattern(): string
    {
        return '/^[1-9]\d{9,29}$/';
    }
}
