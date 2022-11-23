<?php

namespace App\Rules;

use App\Rules\Concerns\DataAware;
use App\Rules\Concerns\ValidatorAware;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;

final class DefaultRule extends Rule implements ImplicitRule, ValidatorAwareRule
{
    // use DataAware;
    use ValidatorAware;

    /**
     * @var null|mixed
     */
    protected $default;

    public function __construct($default)
    {
        $this->default = $default;
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
        if ($value === null) {
            $data = $this->validator->getData();
            $data[$attribute] = $this->default;
            $this->validator->setData($data);
        }

        return true;
    }
}
