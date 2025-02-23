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

use Illuminate\Foundation\Events\MaintenanceModeEnabled;
use Illuminate\Support\Facades\Log;

class MaintenanceModeEnabledNotificationListener
{
    /**
     * Handle the event.
     */
    public function handle(MaintenanceModeEnabled $event): void
    {
        Log::info('Maintenance mode enabled!');
    }
}
