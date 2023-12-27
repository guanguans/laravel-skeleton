<?php

namespace App\Rules;

final class IntegerBooleanRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        return \in_array($value, [0, 1]);
    }
}
