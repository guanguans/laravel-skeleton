<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
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
     * @return list<\Illuminate\Contracts\Validation\ValidationRule|string>
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

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function failedPotentiallyTranslatedString(
        string $attribute,
        mixed $value,
        \Closure $fail
    ): PotentiallyTranslatedString {
        return $fail($attribute, $this->makeAggregateValidator($attribute, $value)->errors()->first($attribute));
    }
}
