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

namespace App\Listeners;

use App\Models\UserLog;
use Illuminate\Auth\Events\Login;
use Stevebauman\Location\Facades\Location;

/**
 * @see https://github.com/MGeurts/genealogy/blob/main/app/Listeners/UserLogin.php
 */
class UserLoginListener
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        return;
        // -----------------------------------------------------------------------
        // log user (seen_at)
        // -----------------------------------------------------------------------
        $event->user->timestamps = false;
        $event->user->seen_at = now()->getTimestamp();
        $event->user->saveQuietly();

        // -----------------------------------------------------------------------
        // log user (only in production)
        // -----------------------------------------------------------------------
        if (app()->isProduction()) {
            if ($position = Location::get()) {
                $countryName = $position->countryName;
                $countryCode = $position->countryCode;
            } else {
                $countryName = null;
                $countryCode = null;
            }

            UserLog::query()->create([
                'user_id' => $event->user->id,
                'country_name' => $countryName,
                'country_code' => $countryCode,
            ]);
        }

        // -----------------------------------------------------------------------
    }
}
