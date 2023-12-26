<?php

namespace App\Rules;

final class BetweenWordsRule extends Rule
{
    public function __construct(protected int $min, protected int $max) {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $count = str_word_count($value);

        return $count >= $this->min && $count <= $this->max;
    }
}
