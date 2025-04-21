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

namespace App\Providers;

use App\Rules\Rule;
use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laragear\Discover\Facades\Discover;

class ValidatorServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    public function boot(): void
    {
        $this->extendValidator();
    }

    public function shouldRegister(): bool
    {
        return true;
    }

    /**
     * Register rule.
     */
    private function extendValidator(): void
    {
        Discover::in('Rules')
            ->instancesOf(Rule::class)
            ->classes()
            ->each(static function (\ReflectionClass $ruleReflectionClass, $ruleClass): void {
                /** @var class-string&Rule $ruleClass */
                Validator::{$ruleClass::extendType()}(
                    $ruleClass::name(),
                    static fn (
                        string $attribute,
                        mixed $value,
                        array $parameters,
                        \Illuminate\Validation\Validator $validator
                    ): bool => tap(new $ruleClass(...$parameters), static function (Rule $rule) use ($validator): void {
                        $rule instanceof ValidatorAwareRule and $rule->setValidator($validator);
                        $rule instanceof DataAwareRule and $rule->setData($validator->getData());
                    })->passes($attribute, $value),
                    $ruleClass::message()
                );
            });
    }
}
