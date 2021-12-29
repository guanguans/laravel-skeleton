<?php

namespace App\Rules;

final class Base64Rule extends Rule
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
        $this->attribute = $attribute;

        return base64_encode(base64_decode($value, true)) === $value;
    }
}
