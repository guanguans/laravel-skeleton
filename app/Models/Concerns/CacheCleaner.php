<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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
