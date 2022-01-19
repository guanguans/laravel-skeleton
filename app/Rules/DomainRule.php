<?php

namespace App\Rules;

final class DomainRule extends RegexRule
{
    protected function pattern(): string
    {
        return '/^([\w-]+\.)*[\w\-]+\.\w{2,10}$/';
    }
}
