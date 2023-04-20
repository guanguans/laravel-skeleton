<?php

namespace App\Listeners;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Foundation\Http\Events\RequestHandled;

class CollectGarbageListener
{
    /**
     * Handle the event.
     *
     * @param  mixed|RequestHandled|CommandFinished  $event
     */
    public function handle($event): void
    {
        // mega bytes
        $garbage = (int) config('app.garbage', 50);
        if ($garbage > 0 && (memory_get_usage() / 1024 / 1024) > $garbage) {
            gc_collect_cycles();
            // gc_mem_caches();
        }
    }
}
