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
use App\Support\Contracts\ShouldRegisterContract;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ExtendServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    public array $bindings = [];
    public array $singletons = [];

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function register(): void
    {
        $this->registerPushDeer();
        $this->registerFaker();
    }

    public function boot(): void {}

    /**
     * @noinspection SenselessMethodDuplicationInspection
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function when(): array
    {
        return [];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function provides(): array
    {
        return [
            PushDeer::class, 'pushdeer',
        ];
    }

    public function shouldRegister(): bool
    {
        return true;
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
