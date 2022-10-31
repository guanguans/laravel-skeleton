<?php

namespace App\Rules;

use Illuminate\Support\Facades\Hash;

final class CurrentUserPasswordRule extends Rule
{
    /**
     * @var string
     */
    protected $passwordField;

    public function __construct(string $passwordField = 'password')
    {
        $this->passwordField = $passwordField;
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
        return Hash::check($value, optional(auth()->user())->{$this->passwordField});
    }
}
