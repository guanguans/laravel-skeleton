<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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
