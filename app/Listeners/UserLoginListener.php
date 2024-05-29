<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Stevebauman\Location\Facades\Location;

/**
 * @see https://github.com/MGeurts/genealogy/blob/main/app/Listeners/UserLogin.php
 */
class UserLoginListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

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

            \App\Models\Userlog::query()->create([
                'user_id' => $event->user->id,
                'country_name' => $countryName,
                'country_code' => $countryCode,
            ]);
        }

        // -----------------------------------------------------------------------
    }
}
