<?php

namespace App\Rules;

final class HtmlcleanRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        return strip_tags($value) == $value;
    }
}
