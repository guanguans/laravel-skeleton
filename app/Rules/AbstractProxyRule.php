<?php

/** @noinspection PhpDeprecationInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Validation\Validator;

abstract class AbstractProxyRule extends AbstractRule
{
    protected ?Validator $proxyValidator = null;

    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        return $this->makeProxyValidator($attribute, $value)->passes();
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function createPotentiallyTranslatedString(string $attribute, mixed $value, \Closure $fail): PotentiallyTranslatedString
    {
        return $fail($attribute, $this->makeProxyValidator($attribute, $value)->errors()->first($attribute));
    }

    protected function makeProxyValidator(string $attribute, mixed $value): Validator
    {
        return $this->proxyValidator ??= ValidatorFactory::make(
            [$attribute => $value],
            $this->rules($attribute),
            $this->messages($attribute),
            $this->attributes($attribute),
        );
    }

    /**
     * @return array<string, (\Closure(string $attribute, mixed $value, Closure $fail): void)|list<mixed>|Rule|string|\Stringable|ValidationRule>
     */
    abstract protected function rules(string $attribute): array;

    /**
     * @return array<string, string>
     *
     * @noinspection PhpUnusedParameterInspection
     */
    protected function messages(string $attribute): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     *
     * @noinspection PhpUnusedParameterInspection
     */
    protected function attributes(string $attribute): array
    {
        return [];
    }
}
