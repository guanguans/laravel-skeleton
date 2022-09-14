<?php

namespace App\Rules\Concerns;

trait ValidatorAware
{
    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Set the current validator.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     *
     * @return $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }
}
