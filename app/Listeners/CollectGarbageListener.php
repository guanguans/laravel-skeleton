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

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Foundation\Http\Events\RequestHandled;

class CollectGarbageListener
{
    /**
     * Handle the event.
     *
     * @param null|CommandFinished|mixed|RequestHandled $event
     */
    public function handle(mixed $event): void
    {
        // mega bytes
        $garbage = (int) config('app.garbage', 50);

        if (0 < $garbage && (memory_get_usage() / 1024 / 1024) > $garbage) {
            gc_collect_cycles();
            // gc_mem_caches();
        }
    }
}
