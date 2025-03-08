<?php

namespace App\Providers;

use App\Support\OpenAI;
use App\Support\PushDeer;
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
        $this->registerOpenAI();
        $this->registerPushDeer();
        $this->registerFaker();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return string[]
     */
    #[\Override]
    public function when(): array
    {
        return [];
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    #[\Override]
    public function provides(): array
    {
        return [
            OpenAI::class, 'openai',
            PushDeer::class, 'pushdeer',
        ];
    }

    private function registerOpenAI(): void
    {
        $this->app->singleton(
            OpenAI::class,
            static fn (Application $application): OpenAI => new OpenAI($application->make(Repository::class)
                ->make('services.openai'))
        );
        $this->app->alias(OpenAI::class, 'openai');
    }

    private function registerPushDeer(): void
    {
        $this->app->singleton(
            PushDeer::class,
            static fn (Application $application): PushDeer => new PushDeer($application->make(Repository::class)
                ->make('services.pushdeer'))
        );
        $this->app->alias(PushDeer::class, 'pushdeer');
    }

    private function registerFaker(): void
    {
        $this->app->singleton(Generator::class, static function () {
            $faker = Factory::create();

            $faker->addProvider(
                new class
                {
                    public function imageUrl(int $width = 640, int $height = 480): string
                    {
                        return \sprintf('https://placekitten.com/%d/%d', $width, $height);
                    }

                    /**
                     * @param  string  $format  raw|full|small|thumb|regular|small_s3
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
