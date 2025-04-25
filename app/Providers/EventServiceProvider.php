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

namespace App\Providers;

use App\Listeners\LogContextSubscriber;
use App\Listeners\MaintenanceModeDisabledNotificationListener;
use App\Listeners\MaintenanceModeEnabledNotificationListener;
use App\Listeners\SetRequestIdListener;
use App\Observers\UserObserver;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Conditionable;

class EventServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * {@inheritDoc}
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    protected $listen = [
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\LogActivity::class.'@login',
        ],
        \Illuminate\Auth\Events\Logout::class => [
            \App\Listeners\LogActivity::class.'@logout',
        ],
        \Illuminate\Auth\Events\Registered::class => [
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
            \App\Listeners\LogActivity::class.'@registered',
        ],
        \Illuminate\Auth\Events\Failed::class => [
            \App\Listeners\LogActivity::class.'@failed',
        ],
        \Illuminate\Auth\Events\PasswordReset::class => [
            \App\Listeners\LogActivity::class.'@passwordReset',
        ],
        \Illuminate\Auth\Events\Attempting::class => [],
        \Illuminate\Auth\Events\Authenticated::class => [],
        \Illuminate\Auth\Events\Validated::class => [],
        \Illuminate\Auth\Events\Verified::class => [],
        \Illuminate\Auth\Events\CurrentDeviceLogout::class => [],
        \Illuminate\Auth\Events\OtherDeviceLogout::class => [],
        \Illuminate\Auth\Events\Lockout::class => [],
        \Illuminate\Foundation\Events\MaintenanceModeEnabled::class => [
            MaintenanceModeEnabledNotificationListener::class,
        ],
        \Illuminate\Foundation\Events\MaintenanceModeDisabled::class => [
            MaintenanceModeDisabledNotificationListener::class,
        ],
        // 'bootstrapping: '.BootProviders::class => [
        //     SetRequestIdListener::class,
        // ],
        // 'bootstrapped: '.BootProviders::class => [
        //     SetRequestIdListener::class,
        // ],
    ];

    /** {@inheritDoc} */
    protected $subscribe = [
        LogContextSubscriber::class,
    ];

    /**
     * {@inheritDoc}
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    protected $observers = [
        \App\Models\User::class => UserObserver::class,
    ];

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, static function (): void {});
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {
            Event::listen('*', static function (string $event, array $data): void {
                if (MessageLogged::class === $event) {
                    return;
                }

                Log::channel('daily-deprecations')->info($event, $data);
            });
        });
    }
}
