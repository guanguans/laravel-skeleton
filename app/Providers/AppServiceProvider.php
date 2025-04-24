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
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

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
        // $this->registerMixins();
        // $this->registerProviders();
        $this->booting(function (): void {
            $this->registerMixins();
            $this->registerProviders();
        });
    }

    private function registerMixins(): void
    {
        classes(
            static fn (
                string $file,
                string $class
            ): bool => str($file)->is('*/../../app/Support/Mixins/*') && str($class)->is('App\\Support\\Mixins\\*')
        )
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
        classes(
            static fn (
                string $file,
                string $class
            ): bool => str($file)->is('*/../../app/Providers/*') && str($class)->is('App\\Providers\\*')
        )
            // ->keys()
            // ->dd()
            ->each(
                fn (\ReflectionClass $reflectionClass): ServiceProvider => $this->app->register(
                    $reflectionClass->getName()
                )
            );
    }
}
