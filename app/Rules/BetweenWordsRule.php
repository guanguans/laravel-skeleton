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

final class BetweenWordsRule extends Rule
{
    public function __construct(private readonly int $min, private readonly int $max) {}

    public function passes(string $attribute, mixed $value): bool
    {
        $count = str_word_count($value);

        return $count >= $this->min && $count <= $this->max;
    }
}
