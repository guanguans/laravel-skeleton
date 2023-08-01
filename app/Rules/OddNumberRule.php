<?php

namespace App\Rules;

final class OddNumberRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^\d*[13579]$/';
    }
}
