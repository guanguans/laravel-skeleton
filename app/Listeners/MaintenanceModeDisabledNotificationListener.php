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

use Illuminate\Foundation\Events\MaintenanceModeDisabled;
use Illuminate\Support\Facades\Log;

class MaintenanceModeDisabledNotificationListener
{
    public function handle(MaintenanceModeDisabled $event): void
    {
        Log::info('Maintenance mode disabled!');
    }
}
