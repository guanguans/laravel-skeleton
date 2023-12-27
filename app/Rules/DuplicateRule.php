<?php

namespace App\Rules;

final class DuplicateRule extends Rule
{
    public function __construct(private readonly ?string $delimiter = null) {}

    /**
     * Determine if the validation rule passes.
     */
    public function passes(string $attribute, mixed $value): bool
    {
        return collect($this->delimiter === null ? str_split($value) : explode($this->delimiter, $value))
            ->duplicates()
            ->isEmpty();
    }
}
