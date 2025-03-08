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

final class Base64Rule extends Rule
{
    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        return base64_encode(base64_decode($value, true)) === $value;
    }
}
