<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;
use InvalidArgumentException;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * 指示验证是否应在第一个规则失败后停止。
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    public function validationData()
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    public function authorize(): bool
    {
        return $this->call(__FUNCTION__, \func_get_args(), true);
    }

    public function rules(): array
    {
        return $this->call(__FUNCTION__, \func_get_args(), []);
    }

    public function messages(): array
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    public function attributes(): array
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    protected function failedValidation(Validator $validator)
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    protected function failedAuthorization()
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    public function validator(ValidationFactory $factory): Validator
    {
        return $this->call(__FUNCTION__, \func_get_args(), $this->createDefaultValidator($factory));
    }

    protected function withValidator(Validator $validator): Validator
    {
        return $this->call(__FUNCTION__, \func_get_args(), $validator);
    }

    /**
     * @return callable|string
     */
    protected function after()
    {
        return $this->call(
            __FUNCTION__,
            \func_get_args(),
            /**
             * @return mixed
             *
             * @throws \Throwable
             */
            static fn (Validator $validator): \Illuminate\Contracts\Validation\Validator => $validator
        );
    }

    protected function call(string $method, array $args = [], $defaultReturn = null)
    {
        $actionMethod = transform($method, function (string $method) {
            if (! \in_array(
                $method,
                [
                    'validationData',
                    'authorize',
                    'rules',
                    'messages',
                    'attributes',
                    'failedValidation',
                    'failedAuthorization',
                    'validator',
                    'withValidator',
                    'after',
                ],
                true
            )) {
                throw new InvalidArgumentException("Can't call the method[$method].");
            }

            return $this->route()?->getActionMethod().ucfirst($method);
        });

        if (method_exists($this, $actionMethod)) {
            return $this->$actionMethod(...$args);
        }

        if (method_exists(parent::class, $method)) {
            return parent::$method(...$args);
        }

        return $defaultReturn;
    }
}
