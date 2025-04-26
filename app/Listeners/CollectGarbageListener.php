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

use Illuminate\Support\Facades\Config;

final class CollectGarbageListener
{
    /**
     * @noinspection PhpUnusedParameterInspection
     *
     * @see \Illuminate\Console\Events\CommandFinished
     * @see \Illuminate\Foundation\Http\Events\RequestHandled
     */
    public function handle(object $event): void
    {
        // mega bytes
        $garbage = Config::integer('app.garbage', 50);

        if (0 < $garbage && (memory_get_usage() / 1024 / 1024) > $garbage) {
            gc_collect_cycles();
            // gc_mem_caches();
        }
    }
}
