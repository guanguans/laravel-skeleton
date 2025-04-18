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

use App\Support\Clients\PushDeer;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class ExtendServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * All of the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public array $bindings = [];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array<array-key, string>
     */
    public array $singletons = [];

    /**
     * Register services.
     */
    #[\Override]
    public function register(): void
    {
        $this->registerPushDeer();
        $this->registerFaker();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return list<string>
     */
    #[\Override]
    public function when(): array
    {
        return [];
    }

    /**
     * Get the services provided by the provider.
     *
     * @return list<string>
     */
    #[\Override]
    public function provides(): array
    {
        return [
            PushDeer::class, 'pushdeer',
        ];
    }

    private function registerPushDeer(): void
    {
        $this->app->singleton(
            PushDeer::class,
            static fn (Application $application): PushDeer => new PushDeer($application->make(Repository::class)->get('services.pushdeer'))
        );
        $this->app->alias(PushDeer::class, 'pushdeer');
    }

    private function registerFaker(): void
    {
        $this->app->singleton(static function (): Generator {
            $faker = Factory::create();

            $faker->addProvider(
                new class {
                    public function imageUrl(int $width = 640, int $height = 480): string
                    {
                        return \sprintf('https://placekitten.com/%d/%d', $width, $height);
                    }

                    /**
                     * @param string $format raw|full|small|thumb|regular|small_s3
                     */
                    public function imageRandomUrl(string $format = 'small'): string
                    {
                        return \sprintf('https://random.danielpetrica.com/api/random?format=%s', $format);
                    }
                }
            );

            return $faker;
        });

        $this->app->alias(Generator::class, 'faker');
    }
}
