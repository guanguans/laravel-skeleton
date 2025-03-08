<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Rules;

class AddressIPV4Rule extends RegexRule
{
    #[\Override]
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/(?:\b25[0-5]|\b2[0-4]\d|\b[01]?\d\d?)(?:\.(25[0-5]|2[0-4]\d|[01]?\d\d?)){3}/';
    }
}
