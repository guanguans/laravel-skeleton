<?php

namespace App\Rules;

final class PhoneRule extends RegexRule
{
    protected function pattern(): string
    {
        return '/^(?:(?:\+|00)86)?1[3-9]\d{9}$/';
    }
}
