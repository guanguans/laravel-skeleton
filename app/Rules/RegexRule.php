<?php

namespace App\Rules;

abstract class RegexRule extends Rule
{
    /**
     * REGEX pattern of rule
     *
     * @var string
     */
    abstract protected function pattern(): string;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;

        return (bool) preg_match($this->pattern(), $value);
    }
}
