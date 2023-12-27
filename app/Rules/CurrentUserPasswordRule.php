<?php

namespace App\Rules;

use Illuminate\Support\Facades\Hash;

final class CurrentUserPasswordRule extends Rule
{
    /**
     * Determine if the validation rule passes.
     */
    public function passes(string $attribute, mixed $value): bool
    {
        return Hash::check($value, optional(auth()->user())->getAuthPassword());
    }
}
