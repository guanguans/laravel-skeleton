<?php

namespace App\Rules;

use Illuminate\Support\Facades\Hash;

final class CurrentUserPasswordRule extends Rule
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
        return Hash::check($value, optional(auth()->user())->getAuthPassword());
    }
}
