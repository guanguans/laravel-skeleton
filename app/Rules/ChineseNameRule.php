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

final class ChineseNameRule extends RegexRule
{
    #[\Override]
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^(?:[\u4e00-\u9fa5·]{2,16})$/';
    }
}
