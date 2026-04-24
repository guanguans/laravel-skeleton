<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Providers;

use App\Rules\AbstractRule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

final class ValidatorServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    /**
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    private function ever(): void
    {
        $this->whenever(true, function (): void {
            $this->defaultPassword();
            $this->extendValidator();
        });
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {});
    }

    private function defaultPassword(): void
    {
        Password::defaults(fn (): Password => Password::min(8)->max(64)->when(
            $this->app->isProduction(),
            static fn (#[\SensitiveParameter] Password $password) => $password
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()
        ));
    }

    /**
     * @throws \ErrorException
     * @throws \ReflectionException
     *
     * @noinspection PhpUndefinedMethodInspection
     */
    private function extendValidator(): void
    {
        classes(
            static fn (string $class, string $file): bool => str($class)->is('App\\Rules\\*')
                && str($file)->is('*/../../app/Rules/*')
        )
            ->filter(
                static fn (\ReflectionClass $reflectionClass): bool => $reflectionClass->isSubclassOf(AbstractRule::class)
                    && $reflectionClass->isInstantiable()
            )
            // ->keys()->dd()
            ->each(
                /**
                 * @param \ReflectionClass<\App\Rules\AbstractRule> $ruleReflectionClass
                 * @param class-string<\App\Rules\AbstractRule> $ruleClass
                 */
                static function (\ReflectionClass $ruleReflectionClass, string $ruleClass): void {
                    ValidatorFacade::{$ruleClass::extendMethod()}(
                        $ruleClass::name(),
                        static fn (string $attribute, mixed $value, array $parameters, Validator $validator): bool => tap(
                            new $ruleClass(...$parameters),
                            static function (AbstractRule $rule) use ($validator): void {
                                $rule instanceof DataAwareRule and $rule->setData($validator->getData());
                                $rule instanceof ValidatorAwareRule and $rule->setValidator($validator);
                            }
                        )->passes($attribute, $value),
                        $ruleClass::message()
                    );
                }
            );
    }
}
