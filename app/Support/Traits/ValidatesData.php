<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Traits;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @mixin \App\Http\Controllers\Controller
 */
trait ValidatesData
{
    /**
     * Run the validation routine against the given validator.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateDataWith(array|Validator $validator, array $data): array
    {
        if (\is_array($validator)) {
            $validator = $this->getValidationDataFactory()->make($data, $validator);
        }

        return $validator->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateData(
        array $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ): array {
        return $this->getValidationDataFactory()->make(
            $data,
            $rules,
            $messages,
            $customAttributes
        )->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateDataWithBag(
        string $errorBag,
        array $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ): array {
        try {
            return $this->validateData($data, $rules, $messages, $customAttributes);
        } catch (ValidationException $validationException) {
            $validationException->errorBag = $errorBag;

            throw $validationException;
        }
    }

    /**
     * Get a validation factory instance.
     */
    protected function getValidationDataFactory(): Factory
    {
        return app(Factory::class);
    }
}
