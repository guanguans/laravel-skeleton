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

final class Base64Rule extends Rule
{
    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        return base64_encode(base64_decode($value, true)) === $value;
    }
}
