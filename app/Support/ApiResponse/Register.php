<?php

declare(strict_types=1);

namespace App\Support\ApiResponse;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;

class Register
{
    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public static function registerDefaultRenderUsing(): void
    {
        app(ExceptionHandler::class)->renderable(self::defaultRenderUsing());
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public static function registerRenderUsing(\Closure $condition): void
    {
        app(ExceptionHandler::class)->renderable(self::renderUsing($condition));
    }

    public static function defaultRenderUsing(): \Closure
    {
        return self::renderUsing(
            static fn (Request $request): bool => $request->is('api/*')
        );
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     *
     * @see \Illuminate\Foundation\Exceptions\Handler::renderable()
     * @see \Illuminate\Foundation\Exceptions\Handler::renderViaCallbacks()
     */
    public static function renderUsing(\Closure $condition): \Closure
    {
        return static function (\Throwable $throwable, Request $request) use ($condition) {
            if (value($condition, $request, $throwable)) {
                self::register();

                return app(ApiResponse::class)->throw($throwable);
            }
        };
    }

    public static function register(): void
    {
        app()->singletonIf(
            ApiResponse::class,
            static fn (): ApiResponse => new ApiResponse(config('api-response.pipes', []))
        );
    }
}
