<?php

namespace App\Rules;

final class DomainRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^([\w-]+\.)*[\w\-]+\.\w{2,10}$/';
    }
}
