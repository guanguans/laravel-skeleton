<?php

namespace App\Listeners;

use Illuminate\Auth\Events as AuthEvents;
use Illuminate\Support\Facades\Log;

/**
 * @see https://gist.github.com/valorin/b90dc36197f47323a9207093f6a6dfc5
 */
class LogActivity
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
        return static::class.'@'.$method;
    }

    private function info(object $event, string $message, array $context = []): void
    {
        $class = class_basename($event::class);

        Log::info("[$class] $message", $context);
    }
}
