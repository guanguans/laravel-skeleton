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
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @mixin \App\Http\Controllers\Controller
 */
trait ValidateStrictAll
{
    /**
     * Run the validation routine against the given validator.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateStrictAllWith(array|Validator $validator, ?Request $request = null): array
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $request = $request ?: \Illuminate\Support\Facades\Request::getFacadeRoot();

        if (\is_array($validator)) {
            $validator = $this->getValidationStrictAllFactory()->make($request->strictAll(), $validator);
        }

        return $validator->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateStrictAll(
        Request $request,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ): array {
        return $this->getValidationStrictAllFactory()->make(
            $request->strictAll(),
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
    public function validateStrictAllWithBag(
        string $errorBag,
        Request $request,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ): array {
        try {
            return $this->validateStrictAll($request, $rules, $messages, $customAttributes);
        } catch (ValidationException $validationException) {
            $validationException->errorBag = $errorBag;

            throw $validationException;
        }
    }

    /**
     * Get a validation factory instance.
     */
    protected function getValidationStrictAllFactory(): Factory
    {
        return app(Factory::class);
    }
}
