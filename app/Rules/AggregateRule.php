<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Rules;

use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Validation\Validator;

abstract class AggregateRule extends Rule
{
    protected Validator $aggregateValidator;

    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        return $this->makeAggregateValidator($attribute, $value)->passes();
    }

    /**
     * @return array<\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    abstract protected function rules(): array;

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

    #[\Override]
    protected function failedPotentiallyTranslatedString(string $attribute, mixed $value, \Closure $fail): PotentiallyTranslatedString
    {
        return $fail($this->makeAggregateValidator($attribute, $value)->errors()->first($attribute));
    }
}
