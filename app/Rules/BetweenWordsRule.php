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

final class BetweenWordsRule extends Rule
{
    public function __construct(
        private readonly int $min,
        private readonly int $max
    ) {}

    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        $count = str_word_count($value);

        return $count >= $this->min && $count <= $this->max;
    }
}
