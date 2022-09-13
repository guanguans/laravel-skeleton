<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Str;

abstract class Rule implements \Illuminate\Contracts\Validation\Rule, DataAwareRule, ValidatorAwareRule
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    abstract public function passes($attribute, $value);

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return sprintf(':attribute 必须是有效的 %s: :input', Str::of($this->getName())->replace('_', ' ')->title());
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?: $this->name = Str::of(class_basename($this))->replaceLast('Rule', '')->snake();
    }

    /**
     * @param  string  $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the current validator.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     *
     * @return $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }
}
