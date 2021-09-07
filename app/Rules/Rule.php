<?php

namespace App\Rules;

abstract class Rule implements \Illuminate\Contracts\Validation\Rule
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    abstract public function passes($attribute, $value);

    /**
     * Get the validation error message.
     *
     * @return string
     */
    abstract public function message();

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
