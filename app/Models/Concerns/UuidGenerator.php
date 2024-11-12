<?php

declare(strict_types=1);

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
        static::creating(static function (self $model) {
            if (Schema::hasColumn($model->getTable(), 'uuid')) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }
}
