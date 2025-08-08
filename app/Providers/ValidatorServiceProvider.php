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
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;
use Laragear\Discover\Facades\Discover;

final class ValidatorServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

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
        Password::defaults(fn (): Password => Password::min(8)
            ->max(255)
            ->when(
                $this->app->isProduction(),
                static fn (#[\SensitiveParameter] Password $password) => $password
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ));
    }

    private function extendValidator(): void
    {
        Discover::in('Rules')
            ->instancesOf(Rule::class)
            ->classes()
            ->each(static function (\ReflectionClass $ruleReflectionClass, string $ruleClass): void {
                /** @var class-string<\App\Rules\Rule> $ruleClass */
                ValidatorFacade::{$ruleClass::extendMethod()}(
                    $ruleClass::name(),
                    static fn (string $attribute, mixed $value, array $parameters, Validator $validator): bool => tap(
                        new $ruleClass(...$parameters),
                        static function (Rule $rule) use ($validator): void {
                            $rule instanceof DataAwareRule and $rule->setData($validator->getData());
                            $rule instanceof ValidatorAwareRule and $rule->setValidator($validator);
                        }
                    )->passes($attribute, $value),
                    $ruleClass::message()
                );
            });
    }
}
