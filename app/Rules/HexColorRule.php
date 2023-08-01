<?php

namespace App\Rules;

class HexColorRule extends RegexRule
{
    /**
     * {@inheritDoc}
     */
    protected function pattern(): string
    {
        return '/^#?(?:[a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/';
    }
}
