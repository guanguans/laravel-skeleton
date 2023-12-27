<?php

namespace App\Rules;

use Illuminate\Support\Facades\Hash;

final class CurrentUserPasswordRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        return Hash::check($value, auth()->user()?->getAuthPassword());
    }
}
