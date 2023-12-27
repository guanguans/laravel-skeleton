<?php

namespace App\Rules;

use App\Rules\Concerns\DataAware;
use App\Rules\Concerns\ValidatorAware;
use Illuminate\Contracts\Validation\ValidatorAwareRule;

final class DefaultRule extends Rule implements ValidatorAwareRule
{
    // use DataAware;
    use ValidatorAware;

    /**
     * Indicates whether the rule should be implicit.
     */
    public bool $implicit = true;

    public function __construct(protected $default) {}

    /**
     * Determine if the validation rule passes.
     */
    public function passes(string $attribute, mixed $value): bool
    {
        if ($value === null) {
            $data = $this->validator->getData();
            $data[$attribute] = $this->default;
            $this->validator->setData($data);
        }

        return true;
    }
}
