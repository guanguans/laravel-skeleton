<?php

declare(strict_types=1);

namespace App\Support\ApiResponse;

use Illuminate\Contracts\Debug\ExceptionHandler;
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
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function packageBooted(): void
    {
        if (config('api-response.register_render_using')) {
            $renderUsing = config('api-response.render_using');
            if (\is_string($renderUsing) && class_exists($renderUsing)) {
                $renderUsing = $this->app->make($renderUsing);
            }

            $this->app->make(ExceptionHandler::class)->renderable($renderUsing);
        }
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
}
