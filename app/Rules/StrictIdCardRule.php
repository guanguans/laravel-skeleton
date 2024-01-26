<?php

namespace App\Rules;

use App\Support\IdCard;

final class StrictIdCardRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        return IdCard::passes($value);
    }
}
