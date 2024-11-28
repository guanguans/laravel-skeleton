<?php

namespace App\Providers;

use App\Listeners\MaintenanceModeDisabledNotificationListener;
use App\Listeners\MaintenanceModeEnabledNotificationListener;
use App\Listeners\SetRequestIdListener;
use App\Listeners\ShareLogContextSubscriber;
use App\Observers\UserObserver;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        \Illuminate\Auth\Events\Login::class => [
            // \App\Listeners\AdoptPurchase::class,
            // \App\Listeners\RegisterForProduct::class,
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

    protected $subscribe = [
        ShareLogContextSubscriber::class,
    ];

    /**
     * @var string[]
     */
    protected $observers = [
        \App\Models\User::class => UserObserver::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
