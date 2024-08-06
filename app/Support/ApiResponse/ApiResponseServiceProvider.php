<?php

declare(strict_types=1);

namespace App\Support\ApiResponse;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Exceptions\Handler;
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
     */
    private function registerRenderUsing(): void
    {
        if (
            ($renderUsingCreator = config('api-response.render_using_creator'))
            && method_exists($exceptionHandler = $this->app->make(ExceptionHandler::class), 'renderable')
        ) {
            if (\is_string($renderUsingCreator) && class_exists($renderUsingCreator)) {
                $renderUsingCreator = $this->app->make($renderUsingCreator);
            }

            /** @var callable(\Throwable, Request): ?JsonResponse $renderUsing */
            $renderUsing = $renderUsingCreator($exceptionHandler);
            if ($renderUsing instanceof \Closure) {
                $renderUsing = $renderUsing->bindTo($exceptionHandler, $exceptionHandler);
            }

            /** @var Handler $exceptionHandler */
            $exceptionHandler->renderable($renderUsing);
        }
    }
}
