<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use App\Rules\BetweenWordsRule;
use App\Rules\CallbackRule;
use App\Rules\ChineseNameRule;
use App\Rules\DefaultRule;
use App\Rules\InstanceofRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

it('can validate rules', function (): void {
    classes(
        static fn (
            string $file,
            string $class
        ): bool => str($file)->is('*/../../app/Rules/*') && str($class)->is('App\\Rules\\*')
    )
        ->filter(fn (ReflectionClass $reflectionClass): bool => $reflectionClass->isInstantiable())
        ->reject(fn (ReflectionClass $reflectionClass): bool => str($reflectionClass->getName())->is([
            BetweenWordsRule::class,
            CallbackRule::class,
            ChineseNameRule::class,
            DefaultRule::class,
            InstanceofRule::class,
        ]))
        ->keys()
        ->tap(function (Collection $ruleClasses): void {
            $validator = Validator::make(
                ['string' => 'string'],
                ['string' => $ruleClasses
                    ->map(
                        /**
                         * @param class-string<\App\Rules\Rule> $ruleClass $ruleClass
                         */
                        static fn (string $ruleClass): string => $ruleClass::name()
                    )
                    ->all()],
            );

            expect($validator->errors()->all())->toBeArray();
        });
})->group(__DIR__, __FILE__);
