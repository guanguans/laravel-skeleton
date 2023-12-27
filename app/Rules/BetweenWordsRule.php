<?php

namespace App\Rules;

final class BetweenWordsRule extends Rule
{
    public function __construct(protected int $min, protected int $max) {}

    /**
     * Determine if the validation rule passes.
     */
    public function passes(string $attribute, mixed $value): bool
    {
        $count = str_word_count($value);

        return $count >= $this->min && $count <= $this->max;
    }
}
