<?php

namespace App\Rules;

final class SlugRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^[a-z0-9]+(?:-[a-z0-9]+)*$/i';
    }
}
