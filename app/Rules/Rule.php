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

namespace App\Rules;

use App\Support\Traits\CreateStaticable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Validation\Concerns\ValidatesAttributes;

abstract class Rule implements ValidationRule
{
    use CreateStaticable;
    // use ValidatesAttributes;

    /**
     * Determine if the validation rule passes.
     */
    abstract public function passes(string $attribute, mixed $value): bool;

    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    #[\Override]
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!$this->passes($attribute, $value)) {
            $this->failedPotentiallyTranslatedString($attribute, $value, $fail)->translate($this->replace());
        }
    }

    public static function name(): string
    {
        return Str::of(class_basename(static::class))->replaceLast('Rule', '')->snake()->toString();
    }

    public static function message(): string
    {
        $transMessage = __($transKey = \sprintf('validation.%s', static::name()));

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
                    $name = __($key = \sprintf('validation.attributes.%s', static::name()));

                    return $name === $key ? str(static::name())->replace('_', ' ') : $name;
                }),
            ]
        );
    }

    protected function failedPotentiallyTranslatedString(
        string $attribute,
        mixed $value,
        \Closure $fail
    ): PotentiallyTranslatedString {
        return $fail(static::message());
    }

    protected function replace(): array
    {
        return [];
    }
}
