<?php

namespace App\Rules;

final class InstanceofRule extends Rule
{
    public function __construct(protected string $class) {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value instanceof $this->class;
    }
}
