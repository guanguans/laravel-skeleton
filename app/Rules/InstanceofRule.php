<?php

namespace App\Rules;

final class InstanceofRule extends Rule
{
    public function __construct(protected string $class) {}

    /**
     * Determine if the validation rule passes.
     */
    public function passes(string $attribute, mixed $value): bool
    {
        return $value instanceof $this->class;
    }
}
