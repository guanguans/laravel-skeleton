<?php

namespace App\Rules;

class CapitalCharWithNumberRule extends RegexRule
{
    /**
     * {@inheritDoc}
     */
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/[A-Z]{2,}-\d+/';
    }
}
