<?php

/** @noinspection PhpUnusedAliasInspection */

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
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

/**
 * @see \Illuminate\Foundation\Http\Kernel::$bootstrappers
 * @see \Illuminate\Foundation\Http\Kernel::bootstrappers()
 * @see \Illuminate\Foundation\Application::bootstrapWith()
 */
class SetRequestIdListener
{
    final public const string REQUEST_ID_NAME = 'X-Request-Id';

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(Application $bootstrapEvent): void
    {
        // $this['events']->dispatch('bootstrapping: '.BootProviders::class, [$this]);
        // $this['events']->dispatch('bootstrapped: '.BootProviders::class, [$this]);

        \define('REQUEST_ID', $requestId = (string) Str::uuid());
        \define('TRACKER_ID', REQUEST_ID);
        $bootstrapEvent->instance(self::REQUEST_ID_NAME, $requestId);
        Request::getFacadeRoot()->headers->set(self::REQUEST_ID_NAME, $bootstrapEvent->make(self::REQUEST_ID_NAME));
    }
}
