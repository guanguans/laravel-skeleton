<?php

namespace App\Rules;

class PostalCodeRule extends RegexRule
{
    protected function pattern(): string
    {
        return '/^(0[1-7]|1[0-356]|2[0-7]|3[0-6]|4[0-7]|5[1-7]|6[1-7]|7[0-5]|8[013-6])\d{4}$/';
    }
}
