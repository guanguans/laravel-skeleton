<?php

namespace App\Rules\Concerns;

use Illuminate\Validation\Validator;

trait ValidatorAware
{
    protected Validator $validator;

    /**
     * Set the current validator.
     */
    public function setValidator(Validator $validator): self
    {
        $this->validator = $validator;

        return $this;
    }
}
