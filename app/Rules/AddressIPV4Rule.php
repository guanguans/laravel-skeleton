<?php

namespace App\Rules;

class AddressIPV4Rule extends RegexRule
{
    /**
     * {@inheritDoc}
     */
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/(?:\b25[0-5]|\b2[0-4]\d|\b[01]?\d\d?)(?:\.(25[0-5]|2[0-4]\d|[01]?\d\d?)){3}/';
    }
}
