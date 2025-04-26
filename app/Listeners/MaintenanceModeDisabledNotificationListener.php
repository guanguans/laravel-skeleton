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

use Illuminate\Foundation\Events\MaintenanceModeDisabled;
use Illuminate\Support\Facades\Log;

final class MaintenanceModeDisabledNotificationListener
{
    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(MaintenanceModeDisabled $event): void
    {
        Log::info('Maintenance mode disabled!');
    }
}
