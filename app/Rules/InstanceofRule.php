<?php

namespace App\Rules;

final class InstanceofRule extends Rule
{
    /**
     * @var string
     */
    protected $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

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
