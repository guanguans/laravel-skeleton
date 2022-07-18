<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function authorize(): bool
    {
        $method = $this->getActionMethod() . 'Authorize';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return true;
    }

    public function rules(): array
    {
        $method = $this->getActionMethod() . 'Rules';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return [];
    }

    public function messages(): array
    {
        $method = $this->getActionMethod() . 'Messages';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return parent::messages();
    }

    /**
     * @inheritdoc
     */
    protected function failedValidation(Validator $validator)
    {
        $method = $this->getActionMethod() . 'FailedValidation';
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
        $method = $this->getActionMethod() . 'FailedAuthorization';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        parent::failedAuthorization();
    }

    /**
     * @return string
     */
    protected function getActionMethod(): string
    {
        return $this->route()->getActionMethod();
    }
}
