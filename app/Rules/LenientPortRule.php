<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Rules;

final class LenientPortRule extends AbstractProxyRule
{
    #[\Override]
    protected function rules(string $attribute): array
    {
        return [$attribute => 'int|between:1,65535'];
    }
}
