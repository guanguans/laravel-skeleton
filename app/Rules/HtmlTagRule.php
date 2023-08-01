<?php

namespace App\Rules;

class HtmlTagRule extends RegexRule
{
    /**
     * {@inheritDoc}
     */
    protected function pattern(): string
    {
        return '/^<([a-z1-6]+)([^<]+)*(?:>(.*)<\/\1>| *\/>)$/';
    }
}
