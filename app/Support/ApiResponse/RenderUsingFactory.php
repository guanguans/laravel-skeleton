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
use Illuminate\Http\Request;

/**
 * @property \Illuminate\Contracts\Container\Container $container
 *
 * @method shouldReturnJson(\Illuminate\Http\Request $request, \Throwable $throwable)
 *
 * @mixin \Illuminate\Foundation\Exceptions\Handler
 */
class RenderUsingFactory
{
    /**
     * @psalm-suppress UndefinedThisPropertyFetch
     * @psalm-suppress InaccessibleProperty
     *
     * @noinspection StaticClosureCanBeUsedInspection
     * @noinspection AnonymousFunctionStaticInspection
     * @noinspection PhpInconsistentReturnPointsInspection
     *
     * @see \App\Support\ApiResponse\ApiResponseServiceProvider::registerRenderUsing()
     */
    public function __invoke(ExceptionHandler $exceptionHandler): \Closure
    {
        /**
         * @return \Illuminate\Http\JsonResponse|void
         */
        return function (\Throwable $throwable, Request $request) {
            try {
                if ($this->shouldReturnJson($request, $throwable)) {
                    return app(ApiResponse::class)->throw($throwable);
                }
            } catch (\Throwable $throwable) {
                // If catch an exception, only report it,
                // and to let the default exception handler handle original exception.
                report($throwable);
            }
        };
    }
}
