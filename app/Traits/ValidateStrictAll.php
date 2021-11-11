<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ValidateStrictAll
{
    /**
     * Run the validation routine against the given validator.
     *
     * @param  \Illuminate\Contracts\Validation\Validator|array  $validator
     * @param  \Illuminate\Http\Request|null  $request
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateStrictAllWith($validator, Request $request = null)
    {
        $request = $request ?: request();

        if (is_array($validator)) {
            $validator = $this->getValidationStrictAllFactory()->make($request->strictAll(), $validator);
        }

        return $validator->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateStrictAll(
        Request $request,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
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
     * @param  string  $errorBag
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateStrictAllWithBag(
        $errorBag,
        Request $request,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        try {
            return $this->validateStrictAll($request, $rules, $messages, $customAttributes);
        } catch (ValidationException $e) {
            $e->errorBag = $errorBag;

            throw $e;
        }
    }

    /**
     * Get a validation factory instance.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidationStrictAllFactory()
    {
        return app(Factory::class);
    }
}
