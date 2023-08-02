<?php

namespace App\Rules;

class HtmlTagRule extends RegexRule
{
    /**
     * {@inheritDoc}
     */
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^<([a-z1-6]+)([^<]+)*(?:>(.*)<\/\1>| *\/>)$/';
    }
}
