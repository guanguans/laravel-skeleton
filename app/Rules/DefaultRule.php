<?php

namespace App\Rules;

use App\Rules\Concerns\DataAware;
use App\Rules\Concerns\ValidatorAware;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;

final class DefaultRule extends ImplicitRule implements DataAwareRule, ValidatorAwareRule
{
    use DataAware;
    use ValidatorAware;

    /**
     * @var null|mixed
     */
    protected $default;

    public function __construct($default = null)
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
        if ($value === null || $value === '') {
            $default = $this->default ?: $value;
            $this->data[$attribute] = $default;
            $this->validator->setData($this->data);
        }

        return true;
    }
}
