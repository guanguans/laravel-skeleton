<?php

namespace App\Rules;

final class StrongPassword extends RegexRule
{
    protected function pattern(): string
    {
        return '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@()$%^&*=_{}[\]:;"\'|\\<>,.\/~`±§+-]).{12,30}$/';
    }
}
