<?php

namespace App\Rules;

final class UuidRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/';
    }
}
