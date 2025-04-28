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

namespace App\Providers;

use App\Listeners\AuthSubscriber;
use App\Listeners\ContextSubscriber;
use App\Observers\UserObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Conditionable;

final class EventServiceProvider extends ServiceProvider
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
        \Illuminate\Auth\Events\Registered::class => [
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
        ],
    ];

    /** {@inheritDoc} */
    protected $subscribe = [
        AuthSubscriber::class,
        ContextSubscriber::class,
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
