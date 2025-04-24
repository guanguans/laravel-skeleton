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
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Laravel\Sanctum\Sanctum;
use Laravel\Telescope\Telescope;
use Opcodes\LogViewer\Facades\LogViewer;

class PackageServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, static function (): void {
            Scramble::configure()->withDocumentTransformers(
                static function (#[\SensitiveParameter] OpenApi $openApi): void {
                    $openApi->secure(SecurityScheme::http('bearer'));
                }
            );
        });
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function never(): void
    {
        $this->whenever(false, function (): void {
            // Passport::enablePasswordGrant();

            Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

            LogViewer::auth(static fn (): bool => RequestFacade::getFacadeRoot()::isAdminDeveloper());

            if (class_exists(Telescope::class)) {
                Telescope::auth(static fn (): bool => RequestFacade::getFacadeRoot()::isAdminDeveloper());
            }

            /**
             * @see https://github.com/AnimeThemes/animethemes-server/blob/main/app/Providers/AppServiceProvider.php
             */
            EnsureFeaturesAreActive::whenInactive(
                static fn (Request $request, array $features): Response => new Response(status: 403)
            );

            $this->whenever($this->isOctaneHttpServer(), static function (): void {
                Event::listen(RequestReceived::class, static function (): void {
                    $uuid = Str::uuid()->toString();

                    if (config('octane.server') === 'roadrunner') {
                        Cache::put($uuid, microtime(true));

                        return;
                    }

                    Cache::store('octane')->put($uuid, microtime(true));
                });
            });
        });
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     */
    private function isOctaneHttpServer(): bool
    {
        return isset($_SERVER['LARAVEL_OCTANE']) || isset($_ENV['OCTANE_DATABASE_SESSION_TTL']);
    }
}
