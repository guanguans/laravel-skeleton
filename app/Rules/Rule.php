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
        return static::fallbackMessage();
    }

    public static function fallbackMessage(): string
    {
        $transMessage = __($transKey = sprintf('validation.%s', $name = static::name()));
        if ($transMessage === $transKey) {
            $transMessage = app()->isLocale('zh_CN')
                ? ':attribute(:input) 必须是有效的 %s。'
                : 'The :attribute(:input) must be a valid %s.';

            $transMessage = sprintf($transMessage, Str::of($name)->replace('_', ' ')->title());
        }

        return $transMessage;
    }

    public static function name(): string
    {
        return Str::of(class_basename(static::class))->replaceLast('Rule', '')->snake();
    }
}
