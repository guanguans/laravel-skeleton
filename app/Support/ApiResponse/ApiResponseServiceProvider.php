<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\ApiResponse;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ApiResponseServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-api-response'); // ->hasConfigFile()
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function packageBooted(): void
    {
        $this->registerRenderUsing();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(
            ApiResponse::class,
            static fn (): ApiResponse => new ApiResponse(
                collect(config('api-response.pipes')),
                collect(config('api-response.exception_map'))
            )
        );
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function registerRenderUsing(): void
    {
        if (
            ($renderUsingFactory = config('api-response.render_using_factory'))
            && ! $this->app->runningInConsole()
            && method_exists($exceptionHandler = $this->app->make(ExceptionHandler::class), 'renderable')
        ) {
            if (\is_string($renderUsingFactory) && class_exists($renderUsingFactory)) {
                $renderUsingFactory = $this->app->make($renderUsingFactory);
            }

            /** @var callable(\Throwable, Request): ?JsonResponse $renderUsing */
            $renderUsing = $renderUsingFactory($exceptionHandler);
            if ($renderUsing instanceof \Closure) {
                $renderUsing = $renderUsing->bindTo($exceptionHandler, $exceptionHandler);
            }

            $exceptionHandler->renderable($renderUsing);
        }
    }
}
