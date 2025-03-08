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

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * @see https://github.com/Zakarialabib/myStockMaster/tree/master/app/Traits
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property string $uuid
 */
trait UuidGenerator
{
    public static function bootUuidGenerator(): void
    {
        static::creating(static function (self $model): void {
            if (Schema::hasColumn($model->getTable(), 'uuid')) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }
}
