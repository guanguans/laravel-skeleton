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

use App\Listeners\MaintenanceModeDisabledNotificationListener;
use App\Listeners\MaintenanceModeEnabledNotificationListener;
use App\Listeners\SetRequestIdListener;
use App\Listeners\ShareLogContextSubscriber;
use App\Observers\UserObserver;
use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
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
        ShareLogContextSubscriber::class,
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
        $this->listenEvents();
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

    public function shouldRegister(): bool
    {
        return true;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function listenEvents(): void
    {
        // $this->app->get('events')->listen(StatementPrepared::class, static function (StatementPrepared $event): void {
        //     $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
        // });

        // $this->app->get('events')->listen(DatabaseBusy::class, static function (DatabaseBusy $event) {
        //     Notification::route('mail', 'dev@example.com')
        //         ->notify(new DatabaseApproachingMaxConnections(
        //             $event->connectionName,
        //             $event->connections
        //         ));
        // });

        $this->app->get(Dispatcher::class)->listen(RequestHandled::class, static function (RequestHandled $event): void {
            if ($event->response instanceof JsonResponse) {
                $event->response->setEncodingOptions($event->response->getEncodingOptions() | \JSON_UNESCAPED_UNICODE);
            }
        });

        // \Illuminate\Support\Facades\Event::listen('*', static function (string $event, array $data): void {
        //     // Log the event class
        //     error_log($event);
        //     // Log the event data delegated to listener parameters
        //     error_log(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS));
        // });
    }
}
