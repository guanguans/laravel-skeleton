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

namespace App\Providers;

use Illuminate\Queue\Events\Looping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class QueueServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        $this->queue();
    }

    /**
     * @see https://github.com/laravel/octane/issues/990
     * @see https://medium.com/@raymondlor/migrating-to-laravel-octane-almost-cost-me-a-client-forever-59b0162e74e2
     * @see https://learnku.com/laravel/t/89636
     */
    private function queue(): void
    {
        Queue::looping(static function (Looping $looping): void {
            while (DB::transactionLevel() > 0) {
                DB::rollBack();
                Log::error('Transaction have not been committed or rolled back.', (array) $looping);
            }
        });
    }
}
