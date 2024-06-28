<?php

namespace App\Listeners;

use Illuminate\Foundation\Events\MaintenanceModeEnabled;
use Illuminate\Support\Facades\Log;

class MaintenanceModeEnabledNotificationListener
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
    public function handle(MaintenanceModeEnabled $event): void
    {
        Log::info('Maintenance mode enabled!');
    }
}
