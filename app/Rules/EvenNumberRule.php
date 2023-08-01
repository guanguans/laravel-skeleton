<?php

namespace App\Rules;

class EvenNumberRule extends RegexRule
{
    /**
     * {@inheritDoc}
     */
    protected function pattern(): string
    {
        return '/^\d*[02468]$/';
    }
}
