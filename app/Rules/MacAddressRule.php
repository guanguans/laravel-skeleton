<?php

namespace App\Rules;

final class MacAddressRule extends Rule
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
        $value = preg_replace("/[\. :-]/i", '', $value);

        return (bool) preg_match('/^[0-9a-f]{12}$/i', $value);
    }
}
