<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

abstract class Rule implements ValidationRule
{
    /**
     * Determine if the validation rule passes.
     */
    abstract public function passes(string $attribute, mixed $value): bool;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail(static::message())->translate();
        }
    }

    public static function name(): string
    {
        return Str::of(class_basename(static::class))->replaceLast('Rule', '')->snake();
    }

    public static function message(): string
    {
        $transMessage = __($transKey = sprintf('validation.%s', static::name()));

        return $transMessage === $transKey ? static::fallbackMessage() : $transMessage;
    }

    public static function extendMethod(): string
    {
        $ruleReflectionClass = new \ReflectionClass(static::class);

        $isImplicit = $ruleReflectionClass->getDefaultProperties()['implicit'] ?? false;
        if ($isImplicit) {
            return 'extendImplicit';
        }

        // extendDependent todo

        return 'extend';
    }

    protected static function fallbackMessage(): string
    {
        return __(
            app()->isLocale('zh_CN')
                ? ':Attribute [:input] 必须是有效的 :Name。'
                : 'The :attribute [:input] must be a valid :Name.',
            [
                'name' => value(static function () {
                    $name = __($key = sprintf('validation.attributes.%s', static::name()));

                    return $name === $key ? str(static::name())->replace('_', ' ') : $name;
                }),
            ]
        );
    }
}
