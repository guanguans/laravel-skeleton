<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Translation\PotentiallyTranslatedString;
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
        return $this->makeAggregateValidator($attribute, $value)->passes();
    }

    protected function makeAggregateValidator(string $attribute, mixed $value): Validator
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

    protected function failedPotentiallyTranslatedString(string $attribute, mixed $value, \Closure $fail): PotentiallyTranslatedString
    {
        return $fail($this->makeAggregateValidator($attribute, $value)->errors()->first($attribute));
    }
}
