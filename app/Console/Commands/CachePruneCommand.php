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

namespace App\Console\Commands;

use Illuminate\Cache\DatabaseStore;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * @see https://github.com/EncoreDigitalGroup/laravel-cache-prune
 * @see https://github.com/jhdxr/laravel-prune-db-cache
 */
final class CachePruneCommand extends Command
{
    protected $signature = 'cache:prune';
    protected $description = 'Prune expired cache entries from the database cache store';

    public function handle(): void
    {
        $cache = Cache::getStore();

        if (!$cache instanceof DatabaseStore) {
            $this->components->error('The cache:prune command only supports the DatabaseStore driver.');

            return;
        }

        $table = Config::get('cache.stores.database.table', 'cache');

        $deleted = DB::table($table)
            ->where('expiration', '<=', Carbon::now()->getTimestamp())
            ->delete();

        $this->components->info("Successfully pruned $deleted expired cache entries from the database.");
    }
}
