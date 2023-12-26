<?php

namespace App\Rules;

use DateTimeZone;

final class TimezoneRule extends Rule
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
        return \in_array($value, DateTimeZone::listIdentifiers());
    }
}
