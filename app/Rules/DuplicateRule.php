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

final class DuplicateRule extends Rule
{
    public function __construct(private readonly ?string $delimiter = null) {}

    public function passes(string $attribute, mixed $value): bool
    {
        return collect($this->delimiter === null ? str_split($value) : explode($this->delimiter, $value))
            ->duplicates()
            ->isEmpty();
    }
}
