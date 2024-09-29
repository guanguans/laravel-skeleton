<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Validation\Validator;

abstract class AggregateRule extends Rule
{
    protected Validator $aggregateValidator;

    /**
     * @return array<\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    abstract protected function rules(): array;

    public function passes(string $attribute, mixed $value): bool
    {
        return $this->validator($attribute, $value)->passes();
    }

    /** @noinspection MissingParentCallInspection */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->validator($attribute, $value)->errors()->first($attribute))->translate();
        }
    }

    protected function validator(string $attribute, mixed $value): Validator
    {
        return $this->aggregateValidator ??= ValidatorFactory::make(
            [$attribute => $value],
            [$attribute => $this->rules()],
            $this->messages(),
            $this->attributes()
        );
    }

    protected function messages(): array
    {
        return [];
    }

    protected function attributes(): array
    {
        return [];
    }
}
