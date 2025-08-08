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
use Illuminate\Support\Str;

it('can validate rules', function (): void {
    classes(
        static fn (
            string $class,
            string $file
        ): bool => str($class)->is('App\\Rules\\*') && str($file)->is('*/../../app/Rules/*')
    )
        ->filter(fn (ReflectionClass $reflectionClass): bool => $reflectionClass->isInstantiable())
        ->reject(fn (ReflectionClass $reflectionClass): bool => str($reflectionClass->getName())->is([
            BetweenWordsRule::class,
            CallbackRule::class,
            // ChineseNameRule::class,
            DefaultRule::class,
            InstanceofRule::class,
        ]))
        ->keys()
        ->tap(function (Collection $ruleClasses): void {
            $validator = Validator::make(
                [
                    'all' => 'all value',
                    'between_words' => 'between words value',
                    'callback' => 'callback value',
                    'default' => null,
                    'instanceof' => 'instanceof value',
                ],
                [
                    'all' => $ruleClasses
                        ->map(
                            /**
                             * @param class-string<\App\Rules\AbstractRule> $ruleClass
                             */
                            static fn (string $ruleClass): string => $ruleClass::name()
                        )
                        ->all(),
                    'between_words' => 'between_words:6,10',
                    'callback' => \sprintf('callback:%s::contains,0,foo', Str::class),
                    'default' => 'default:default value',
                    'instanceof' => 'instanceof:stdClass',
                ],
            );

            expect($validator->errors()->all())
                // ->dd()
                ->toBeArray();
        });
})->group(__DIR__, __FILE__);
