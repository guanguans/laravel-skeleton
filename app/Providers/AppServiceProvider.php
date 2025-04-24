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
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Laragear\Discover\Facades\Discover as LaragearDiscover;
use Spatie\StructureDiscoverer\Discover as SpatieDiscover;

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

    private function registerMixins(): void
    {
        // classes(static fn (string $file, string $class): bool => str($class)->is('App\\Support\\Mixins\\*'))
        // LaragearDiscover::in('Support/Mixins')
        collect(
            SpatieDiscover::in(__DIR__.'/../Support/Mixins/')
                // ->parallel()
                // ->useReflection(__DIR__.'/../Support/Mixins/', 'App\\Support\\Mixins')
                ->classes()
                ->get()
        )->mapWithKeys(static fn (string $class): array => [$class => new \ReflectionClass($class)])
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
        LaragearDiscover::in('Providers')
            ->instancesOf(ServiceProvider::class)
            ->classes()
            ->keys()
            // ->dd()
            ->each(fn (string $class): ServiceProvider => $this->app->register($class));
    }
}
