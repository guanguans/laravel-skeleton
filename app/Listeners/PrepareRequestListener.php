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

namespace App\Listeners;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @see \Illuminate\Foundation\Http\Kernel::$bootstrappers
 * @see \Illuminate\Foundation\Http\Kernel::bootstrappers()
 * @see \Illuminate\Foundation\Application::bootstrapWith()
 */
final class PrepareRequestListener
{
    public const string X_REQUEST_ID = 'X-Request-Id';

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __invoke(Application $app): void
    {
        \defined('TRACE_ID') or \define('TRACE_ID', (string) Str::uuid());

        if (!$app->runningInConsole()) {
            $app->make(Request::class)->headers->set(self::X_REQUEST_ID, TRACE_ID);

            if ($app->make(Request::class)->is('api/*')) {
                $app->make(Request::class)->headers->set('Accept', 'application/json');
            }
        }
    }
}
