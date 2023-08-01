<?php

namespace App\Rules;

class CapitalCharWithNumberRule extends RegexRule
{
    /**
     * {@inheritDoc}
     */
    protected function pattern(): string
    {
        return '/[A-Z]{2,}-\d+/';
    }
}
