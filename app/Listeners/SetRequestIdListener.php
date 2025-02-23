<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Listeners;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Support\Str;

/**
 * @see \Illuminate\Foundation\Http\Kernel::$bootstrappers
 * @see \Illuminate\Foundation\Http\Kernel::bootstrappers()
 * @see \Illuminate\Foundation\Application::bootstrapWith()
 */
class SetRequestIdListener
{
    final public const REQUEST_ID_NAME = 'X-Request-Id';

    /**
     * Handle the event.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(Application $bootstrapEvent): void
    {
        // $this['events']->dispatch('bootstrapping: '.BootProviders::class, [$this]);
        // $this['events']->dispatch('bootstrapped: '.BootProviders::class, [$this]);

        \define('REQUEST_ID', $requestId = (string) Str::uuid());
        \define('TRACKER_ID', REQUEST_ID);
        $bootstrapEvent->instance(self::REQUEST_ID_NAME, $requestId);
        request()->headers->set(self::REQUEST_ID_NAME, $bootstrapEvent->make(self::REQUEST_ID_NAME));
    }
}
