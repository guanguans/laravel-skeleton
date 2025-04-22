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

use App\Models\PersonalAccessToken;
use App\Support\Clients\PushDeer;
use App\Support\Contracts\ShouldRegisterContract;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Laravel\Sanctum\Sanctum;
use Laravel\Telescope\Telescope;
use Opcodes\LogViewer\Facades\LogViewer;

class ExtendServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    use Conditionable {
        Conditionable::when as whenever;
    }
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

    public function boot(): void
    {
        // Passport::enablePasswordGrant();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Scramble::configure()->withDocumentTransformers(static function (OpenApi $openApi): void {
            $openApi->secure(SecurityScheme::http('bearer'));
        });
        LogViewer::auth(static fn (): bool => \Illuminate\Support\Facades\Request::getFacadeRoot()::isAdminDeveloper());
        class_exists(Telescope::class) and Telescope::auth(static fn (): bool => \Illuminate\Support\Facades\Request::getFacadeRoot()::isAdminDeveloper());

        /** @see https://github.com/AnimeThemes/animethemes-server/blob/main/app/Providers/AppServiceProvider.php */
        EnsureFeaturesAreActive::whenInactive(static fn (Request $request, array $features): Response => new Response(status: 403));
    }

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
