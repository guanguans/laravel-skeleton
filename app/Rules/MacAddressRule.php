<?php

namespace App\Rules;

final class MacAddressRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        $value = preg_replace("/[\. :-]/i", '', $value);

        return (bool) preg_match('/^[0-9a-f]{12}$/i', $value);
    }
}
