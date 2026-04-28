<?php

/** @noinspection ContractViolationInspection */
/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Rules;

use App\Support\Trait\MakeStaticable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Validation\Concerns\ValidatesAttributes;

abstract class AbstractRule implements ValidationRule
{
    use MakeStaticable;
    // use ValidatesAttributes;

    public bool $implicit = false;

    /**
     * @see https://github.com/egulias/EmailValidator
     * @see https://github.com/Respect/Validation
     * @see https://github.com/ronanguilloux/IsoCodes
     * @see https://github.com/vlucas/valitron
     * @see https://github.com/Wixel/GUMP
     *
     * @param \Closure(string, null|string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     *
     * @noinspection RedundantDocCommentTagInspection
     */
    #[\Override]
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!$this->passes($attribute, $value)) {
            $this
                ->createPotentiallyTranslatedString($attribute, $value, $fail)
                ->translate($this->replace($attribute, $value));
        }
    }

    /**
     * Determine if the validation rule passes.
     */
    abstract public function passes(string $attribute, mixed $value): bool;

    /**
     * @see \Illuminate\Support\Facades\Validator
     * @see \\Illuminate\Validation\Factory
     *
     * @todo extendDependent、replacer
     */
    public static function extendMethod(): string
    {
        return (new \ReflectionClass(static::class)->getDefaultProperties()['implicit'] ?? false) ? 'extendImplicit' : 'extend';
    }

    public static function name(): string
    {
        return str(static::class)->classBasename()->chopEnd('Rule')->snake()->toString();
    }

    public static function message(): string
    {
        $transKey = \sprintf('validation.%s', static::name());
        $transMessage = __($transKey);

        return $transMessage !== $transKey ? $transMessage : static::fallbackMessage();
    }

    protected static function fallbackMessage(): string
    {
        return __(
            app()->isLocale('zh_CN') ? ':Attribute [:input] 必须是有效的 :Name。' : 'The :attribute [:input] must be a valid :Name.',
            [
                'name' => value(static function () {
                    $transNameKey = \sprintf('validation.attributes.%s', static::name());
                    $transNameMessage = __($transNameKey);

                    return $transNameMessage !== $transNameKey ? $transNameMessage : str(static::name())->replace('_', ' ');
                }),
            ]
        );
    }

    /**
     * @see \Illuminate\Translation\CreatesPotentiallyTranslatedStrings::pendingPotentiallyTranslatedString()
     * @see \Illuminate\Validation\ClosureValidationRule::passes()
     * @see \Illuminate\Validation\InvokableValidationRule::passes()
     */
    protected function createPotentiallyTranslatedString(string $attribute, mixed $value, \Closure $fail): PotentiallyTranslatedString
    {
        return $fail($attribute, static::message());
    }

    /**
     * @return array<string, mixed>
     */
    protected function replace(string $attribute, mixed $value): array
    {
        return ['attribute' => $attribute, 'value' => $value];
    }
}
