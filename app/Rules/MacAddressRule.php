<?php

namespace App\Rules;

class MacAddressRule extends Rule
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

        $value = preg_replace("/[\. :-]/i", '', $value);

        return preg_match("/^[0-9a-f]{12}$/i", $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid MAC address.';
    }
}
