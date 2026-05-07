<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use App\Rules\AbstractRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

it('can validate rules', function (): void {
    classes(
        static fn (string $class, string $file): bool => str($class)->is('App\\Rules\\*')
            && str($file)->is('*/../../app/Rules/*')
    )
        ->filter(
            fn (ReflectionClass $reflectionClass): bool => $reflectionClass->isSubclassOf(AbstractRule::class)
                && $reflectionClass->isInstantiable()
                && (
                    !$reflectionClass->getConstructor() instanceof ReflectionMethod
                    || $reflectionClass->getConstructor()->getNumberOfRequiredParameters() === 0
                )
        )
        ->keys()
        ->tap(
            function (Collection $ruleClasses): void {
                $validator = Validator::make(
                    [
                        'all' => 'all value',
                        'between_words' => 'between words value',
                        'callback' => 'callback value',
                        'default' => null,
                        'instanceof' => 'instanceof value',
                    ],
                    [
                        'all' => $ruleClasses->map(static fn (string $ruleClass): string => $ruleClass::name())->all(),
                        'between_words' => 'between_words:6,10',
                        'callback' => \sprintf('callback:%s::contains,0,foo', Str::class),
                        'default' => 'default:default value',
                        'instanceof' => 'instanceof:stdClass',
                    ],
                );

                expect($validator->errors()->all())->toBeArray();
            }
        );
})->group(__DIR__, __FILE__);
