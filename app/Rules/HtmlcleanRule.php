<?php

namespace App\Rules;

final class HtmlcleanRule extends Rule
{
    /**
     * Determine if the validation rule passes.
     */
    public function passes(string $attribute, mixed $value): bool
    {
        return strip_tags($value) == $value;
    }
}
