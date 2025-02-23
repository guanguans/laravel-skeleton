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

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Foundation\Http\Events\RequestHandled;

class CollectGarbageListener
{
    /**
     * Handle the event.
     *
     * @param  null|CommandFinished|mixed|RequestHandled  $event
     */
    public function handle(mixed $event): void
    {
        // mega bytes
        $garbage = (int) config('app.garbage', 50);
        if ($garbage > 0 && (memory_get_usage() / 1024 / 1024) > $garbage) {
            gc_collect_cycles();
            // gc_mem_caches();
        }
    }
}
