<?php

namespace App\Rules;

final class InstanceofRule extends Rule
{
    public function __construct(protected string $class) {}

    public function passes(string $attribute, mixed $value): bool
    {
        return $value instanceof $this->class;
    }
}
