<?php

namespace App\Rules;

final class JwtRule extends RegexRule
{
    protected function pattern(): string
    {
        return '/^[a-zA-Z0-9-_]+\.[a-zA-Z0-9-_]+\.[a-zA-Z0-9-_]+$/';
    }
}
