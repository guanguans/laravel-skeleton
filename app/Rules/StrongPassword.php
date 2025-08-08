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

final class StrongPassword extends AbstractRegexRule
{
    #[\Override]
    protected function pattern(): string
    {
        // 最少 8 位，包括至少 1 个大写字母，1 个小写字母，1 个数字，1 个特殊字符
        /** @lang PhpRegExp */
        return '/^\S*(?=\S{8,})(?=\S*\d)(?=\S*[A-Z])(?=\S*[a-z])(?=\S*[!@#$%^&*? ])\S*$/';
    }
}
