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

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Artisan;

/**
 * @see https://github.com/Zakarialabib/myStockMaster/tree/master/app/Traits
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait CacheCleaner
{
    public static function bootCacheCleaner(): void
    {
        self::created(static function (): void {
            Artisan::call('cache:clear');
        });

        self::updated(static function (): void {
            Artisan::call('cache:clear');
        });

        self::deleted(static function (): void {
            Artisan::call('cache:clear');
        });
    }
}
