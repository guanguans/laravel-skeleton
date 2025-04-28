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

use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\CurrentDeviceLogout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\OtherDeviceLogout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Validated;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;

/**
 * @see https://gist.github.com/valorin/b90dc36197f47323a9207093f6a6dfc5
 */
final class AuthSubscriber
{
    public function subscribe(): array
    {
        return collect([
            Attempting::class,
            Authenticated::class,
            CurrentDeviceLogout::class,
            Failed::class,
            Lockout::class,
            Login::class,
            Logout::class,
            OtherDeviceLogout::class,
            PasswordReset::class,
            PasswordResetLinkSent::class,
            Registered::class,
            Validated::class,
            Verified::class,
        ])->mapWithKeys(static fn (string $eventClass): array => [
            $eventClass => static function (object $event): void {
                /** @var Attempting|Authenticated|CurrentDeviceLogout|Failed|Lockout|Login|Logout|OtherDeviceLogout|PasswordReset|PasswordResetLinkSent|Registered|Validated|Verified| $event */
                Log::info(
                    $event::class,
                    collect($event->user ?? $event->credentials ?? $event->request ?? [])
                        ->except([
                            'password',
                            'password_confirmation',
                        ])
                        ->all()
                );
            },
        ])->all();
    }
}
