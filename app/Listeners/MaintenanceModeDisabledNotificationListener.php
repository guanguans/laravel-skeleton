<?php

namespace App\Listeners;

use Illuminate\Foundation\Events\MaintenanceModeDisabled;
use Illuminate\Support\Facades\Log;

class MaintenanceModeDisabledNotificationListener
{
    public function handle(MaintenanceModeDisabled $event): void
    {
        Log::info('Maintenance mode disabled!');
    }
}
