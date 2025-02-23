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

final class DomainRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^([\w-]+\.)*[\w\-]+\.\w{2,10}$/';
    }
}
