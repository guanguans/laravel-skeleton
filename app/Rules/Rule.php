<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

abstract class Rule implements ValidationRule
{
    /**
     * Determine if the validation rule passes.
     */
    abstract public function passes($attribute, $value);

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message())->translate();
        }
    }

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
