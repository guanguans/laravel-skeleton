<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Providers;

use App\Listeners\ContextSubscriber;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
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

    /** {@inheritDoc} */
    #[\Override]
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /** {@inheritDoc} */
    #[\Override]
    protected $subscribe = [
        ContextSubscriber::class,
    ];

    /** {@inheritDoc} */
    #[\Override]
    protected $observers = [
        User::class => UserObserver::class,
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
                if (MessageLogged::class !== $event) {
                    Log::debug($event, $data);
                }
            });
        });
    }
}
