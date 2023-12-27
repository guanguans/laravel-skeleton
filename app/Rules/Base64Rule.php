<?php

namespace App\Rules;

final class Base64Rule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        return base64_encode(base64_decode($value, true)) === $value;
    }
}
