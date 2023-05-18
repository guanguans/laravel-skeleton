<?php

namespace App\Rules;

use Illuminate\Support\Str;

abstract class Rule implements \Illuminate\Contracts\Validation\Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
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
        return static::localizedMessage();
    }

    public static function localizedMessage(): string
    {
        $transMessage = __($transKey = sprintf('validation.%s', static::name()));

        return $transMessage === $transKey ? static::fallbackMessage() : $transMessage;
    }

    protected static function fallbackMessage(): string
    {
        $transKey = app()->isLocale('zh_CN')
            ? ':Attribute(:input) 必须是有效的 :Name。'
            : 'The :attribute(:input) must be a valid :Name.';

        return __($transKey, [
            'name' => Str::of(static::name())->replace('_', ' '),
        ]);
    }

    public static function name(): string
    {
        return Str::of(class_basename(static::class))->replaceLast('Rule', '')->snake();
    }
}
