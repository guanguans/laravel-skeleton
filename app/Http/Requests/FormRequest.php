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

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * 指示验证是否应在第一个规则失败后停止。
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    #[\Override]
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

    #[\Override]
    public function messages(): array
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    #[\Override]
    public function attributes(): array
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    public function validator(ValidationFactory $factory): Validator
    {
        return $this->call(__FUNCTION__, \func_get_args(), $this->createDefaultValidator($factory));
    }

    #[\Override]
    protected function failedValidation(Validator $validator)
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    #[\Override]
    protected function failedAuthorization()
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    protected function withValidator(Validator $validator): Validator
    {
        return $this->call(__FUNCTION__, \func_get_args(), $validator);
    }

    protected function after(): callable|string
    {
        return $this->call(
            __FUNCTION__,
            \func_get_args(),
            /**
             * @throws \Throwable
             *
             * @return mixed
             */
            static fn (Validator $validator): Validator => $validator
        );
    }

    #[\Override]
    protected function prepareForValidation()
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    #[\Override]
    protected function passedValidation()
    {
        return $this->call(__FUNCTION__, $args = \func_get_args(), parent::{__FUNCTION__}(...$args));
    }

    protected function call(string $method, array $args = [], $defaultReturn = null)
    {
        $actionMethod = transform($method, function (string $method): string {
            throw_unless(\in_array(
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
                    'prepareForValidation',
                    'passedValidation',
                ],
                true
            ), \InvalidArgumentException::class, "Can't call the method[$method].");

            return $this->route()?->getActionMethod().ucfirst($method);
        });

        if (method_exists($this, $actionMethod)) {
            return $this->{$actionMethod}(...$args);
        }

        if (method_exists(parent::class, $method)) {
            return parent::$method(...$args);
        }

        return $defaultReturn;
    }
}
