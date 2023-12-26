<?php

namespace App\Rules;

final class DuplicateRule extends Rule
{
    public function __construct(private readonly ?string $delimiter = null) {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return collect($this->delimiter === null ? str_split($value) : explode($this->delimiter, $value))
            ->duplicates()
            ->isEmpty();
    }
}
