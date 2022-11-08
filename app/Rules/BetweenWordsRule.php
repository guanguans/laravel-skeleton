<?php

namespace App\Rules;

final class BetweenWordsRule extends Rule
{
    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

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
