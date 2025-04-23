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

use App\Support\Attributes\Mixin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\EnumeratesValues;
use Illuminate\Support\Traits\Macroable;
use Laragear\Discover\Facades\Discover;

class AppServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function register(): void
    {
        $this->booting(function (): void {
            $this->registerMixins();
            $this->registerProviders();
        });
    }

    public function boot(): void
    {
        // Discover::at(
        //     'vendor/laravel/framework/src/Illuminate/Support/Facades/',
        //     'Illuminate\\Support\\Facades'
        // )
        //     ->classes()
        //     ->dd();

        classes()
            ->filter(function (string $class) {
                return str($class)->is([
                    Model::class,
                    'Illuminate\\*',
                ]) && !str($class)->is([
                    Str::class,
                    Arr::class,
                    Carbon::class,
                    Number::class,
                    Enumerable::class,
                    EnumeratesValues::class,
                ]);
            })
            ->map(static function (string $class) {
                if (is_subclass_of($class, Facade::class)) {
                    $prefix = ' * @see \\';

                    $seeClass = str((new \ReflectionClass($class))->getDocComment())
                        ->explode(\PHP_EOL)
                        ->filter(fn (string $line) => str($line)->startsWith($prefix))
                        ->pipe(fn (Collection $collection): string => Str::remove($prefix, $collection->firstOrFail()));

                    return [
                        new \ReflectionClass($class),
                        new \ReflectionClass($seeClass),
                    ];
                }

                return new \ReflectionClass($class);
            })
            ->flatten()
            ->mapWithKeys(fn (\ReflectionClass $reflectionClass) => [
                $reflectionClass->getName() => collect($reflectionClass->getMethods(\ReflectionMethod::IS_STATIC))
                    ->filter(
                        fn (\ReflectionMethod $reflectionMethod): bool => $reflectionMethod->isPublic()
                            && !str($reflectionMethod->getName())->is([
                                '__callStatic',
                                ...collect([
                                    Facade::class,
                                    Macroable::class,
                                ])->map(function (string $exceptClass) {
                                    return collect(
                                        (new \ReflectionClass($exceptClass))->getMethods(\ReflectionMethod::IS_STATIC)
                                    )->map->getName();
                                })->flatten()->all(),
                            ])
                    )
                    ->map(
                        static fn (
                            \ReflectionMethod $reflectionMethod
                        ) => "{$reflectionClass->getName()}::{$reflectionMethod->getName()}"
                    )
                    ->all(),
            ])
            ->filter()
            // ->dd()
            ->tap();
    }

    private function registerMixins(): void
    {
        Discover::in('Support/Mixins')
            ->allClasses()
            // ->keys()
            // ->dd()
            ->each(static function (\ReflectionClass $mixinReflectionClass, string $mixinClass): void {
                foreach ($mixinReflectionClass->getAttributes(Mixin::class) as $mixinReflectionAttribute) {
                    /** @var \App\Support\Attributes\Mixin $mixinAttribute */
                    $mixinAttribute = $mixinReflectionAttribute->newInstance();

                    /** @noinspection PhpAccessStaticViaInstanceInspection */
                    $mixinAttribute->class::mixin(resolve($mixinClass), $mixinAttribute->replace);
                }
            });
    }

    private function registerProviders(): void
    {
        Discover::in('Providers')
            ->instancesOf(ServiceProvider::class)
            ->classes()
            ->keys()
            // ->dd()
            ->each(fn (string $class): ServiceProvider => $this->app->register($class));
    }
}
