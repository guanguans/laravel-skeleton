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

    public function passes($attribute, $value)
    {
        return (bool) preg_match($this->pattern(), $value);
    }
}
