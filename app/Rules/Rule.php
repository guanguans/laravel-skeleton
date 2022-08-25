<?php

namespace App\Rules;

use Illuminate\Support\Str;

abstract class Rule implements \Illuminate\Contracts\Validation\Rule
{
    /**
     * @var string
     */
    protected $name;

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
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
