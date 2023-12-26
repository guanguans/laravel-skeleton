<?php

namespace App\Rules;

final class IntegerBooleanRule extends Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return \in_array($value, [0, 1]);
    }
}
