<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

trait ValidatesData
{
    /**
     * Run the validation routine against the given validator.
     *
     * @param  \Illuminate\Contracts\Validation\Validator|array  $validator
     * @param  array  $data
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateDataWith($validator, array $data)
    {
        if (is_array($validator)) {
            $validator = $this->getValidationDataFactory()->make($data, $validator);
        }

        return $validator->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateData(
        array $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
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
     * @param  string  $errorBag
     * @param  array $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateDataWithBag(
        $errorBag,
        array $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        try {
            return $this->validateData($data, $rules, $messages, $customAttributes);
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
    protected function getValidationDataFactory()
    {
        return app(Factory::class);
    }
}
