<?php

namespace App\Rules;

final class PhoneRule extends RegexRule
{
    protected function pattern(): string
    {
        return '/^1(3[0-9]|4[57]|5[0-35-9]|6[6]|7[0135678]|8[0-9])\d{8}$/';
    }
}
