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

use App\Support\Attribute\Mixin;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

final class AppServiceProvider extends ServiceProvider
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

    /**
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    private function registerMixins(): void
    {
        classes(
            static fn (string $class, string $file): bool => str($class)->is('App\\Support\\Mixin\\*')
                && str($file)->is('*/../../app/Support/Mixin/*')
        )
            // ->keys()
            // ->dd()
            ->each(static function (\ReflectionClass $mixinReflectionClass, string $mixinClass): void {
                foreach ($mixinReflectionClass->getAttributes(Mixin::class) as $mixinReflectionAttribute) {
                    $mixinAttribute = $mixinReflectionAttribute->newInstance();
                    \assert($mixinAttribute instanceof Mixin);

                    /** @noinspection PhpAccessStaticViaInstanceInspection */
                    $mixinAttribute->class::mixin(resolve($mixinClass), $mixinAttribute->replace);
                }
            });
    }

    /**
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    private function registerProviders(): void
    {
        classes(
            static fn (string $class, string $file): bool => str($class)->is('App\\Providers\\*')
                && str($file)->is('*/../../app/Providers/*')
        )
            // ->keys()
            // ->dd()
            ->each(fn (\ReflectionClass $reflectionClass): ServiceProvider => $this->app->register($reflectionClass->getName()));
    }
}
