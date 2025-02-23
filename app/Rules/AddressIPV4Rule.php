<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Rules;

class AddressIPV4Rule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/(?:\b25[0-5]|\b2[0-4]\d|\b[01]?\d\d?)(?:\.(25[0-5]|2[0-4]\d|[01]?\d\d?)){3}/';
    }
}
