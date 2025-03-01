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

class CamelCaseRule extends RegexRule
{
    #[\Override]
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^(?:\p{Lu}?\p{Ll}+)(?:\p{Lu}\p{Ll}+)*$/u';
    }
}
