<?php

namespace App\Providers;

use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * @var string[]
     */
    protected $observers = [
        \App\Models\User::class => UserObserver::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->registerObservers();
    }

    /**
     * Register observers.
     */
    protected function registerObservers(): void
    {
        foreach ($this->observers as $class => $observer) {
            /* @var \Illuminate\Database\Eloquent\Model $class */
            $class::observe($observer);
        }
    }
}
