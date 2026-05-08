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

final class BetweenWordsRule extends AbstractRule
{
    private readonly int $min;
    private readonly int $max;

    /**
     * @param int|numeric-string $min
     * @param int|numeric-string $max
     */
    public function __construct(int|string $min, int|string $max)
    {
        $this->max = (int) $max;
        $this->min = (int) $min;
    }

    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        $count = str_word_count((string) $value);

        return $count >= $this->min && $count <= $this->max;
    }
}
