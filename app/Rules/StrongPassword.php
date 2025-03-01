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

final class StrongPassword extends RegexRule
{
    #[\Override]
    protected function pattern(): string
    {
        // 最少 8 位，包括至少 1 个大写字母，1 个小写字母，1 个数字，1 个特殊字符
        /** @lang PhpRegExp */
        return '/^\S*(?=\S{8,})(?=\S*\d)(?=\S*[A-Z])(?=\S*[a-z])(?=\S*[!@#$%^&*? ])\S*$/';
    }
}
