<?php

namespace App\Rules;

use DateTimeZone;

final class TimezoneRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        return \in_array($value, DateTimeZone::listIdentifiers());
    }
}
