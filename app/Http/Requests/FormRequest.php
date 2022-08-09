<?php

namespace App\Http\Requests;

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

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $method = $this->getHandleMethod(__FUNCTION__);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return parent::validationData();
    }

    public function authorize(): bool
    {
        $method = $this->getHandleMethod(__FUNCTION__);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return true;
    }

    public function rules(): array
    {
        $method = $this->getHandleMethod(__FUNCTION__);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return [];
    }

    public function messages(): array
    {
        $method = $this->getHandleMethod(__FUNCTION__);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return parent::messages();
    }

    public function attributes(): array
    {
        $method = $this->getHandleMethod(__FUNCTION__);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return parent::attributes();
    }

    /**
     * @inheritdoc
     */
    protected function failedValidation(Validator $validator)
    {
        $method = $this->getHandleMethod(__FUNCTION__);
        if (method_exists($this, $method)) {
            return $this->$method($validator);
        }

        parent::failedValidation($validator);
    }

    /**
     * @inheritdoc
     */
    protected function failedAuthorization()
    {
        $method = $this->getHandleMethod(__FUNCTION__);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        parent::failedAuthorization();
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return Validator
     */
    public function withValidator($validator)
    {
        return $validator;
    }

    /**
     * @param  string  $type
     *
     * @return string
     */
    protected function getHandleMethod(string $type): string
    {
        if (! in_array(lcfirst($type), ['validationData', 'authorize', 'rules', 'messages', 'attributes', 'failedValidation', 'failedAuthorization'], true)) {
            throw new InvalidArgumentException('Invalid type');
        }

        return optional($this->route())->getActionMethod().ucfirst($type);
    }
}
