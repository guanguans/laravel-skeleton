<?php

/** @noinspection PhpPossiblePolymorphicInvocationInspection */

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

use Illuminate\Auth\Events as AuthEvents;
use Illuminate\Support\Facades\Log;

/**
 * @see https://gist.github.com/valorin/b90dc36197f47323a9207093f6a6dfc5
 */
final class LogActivity
{
    public function login(AuthEvents\Login $event): void
    {
        $this->info($event, "User {$event->user->email} logged in", $event->user->only('id', 'email'));
    }

    public function logout(AuthEvents\Logout $event): void
    {
        $this->info($event, "User {$event->user->email} logged out", $event->user->only('id', 'email'));
    }

    public function registered(AuthEvents\Registered $event): void
    {
        $this->info($event, "User registered: {$event->user->email}");
    }

    public function failed(AuthEvents\Failed $event): void
    {
        $this->info($event, "User {$event->credentials['email']} login failed", ['email' => $event->credentials['email']]);
    }

    public function passwordReset(AuthEvents\PasswordReset $event): void
    {
        $this->info($event, "User {$event->user->email} password reset", $event->user->only('id', 'email'));
    }

    public static function callbackFor(string $method): string
    {
        return self::class.'@'.$method;
    }

    private function info(object $event, string $message, array $context = []): void
    {
        $class = class_basename($event::class);

        Log::info("[$class] $message", $context);
    }
}
