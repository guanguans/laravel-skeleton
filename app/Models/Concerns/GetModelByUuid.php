<?php

declare(strict_types=1);

namespace App\Models\Concerns;

/**
 * @see https://github.com/Zakarialabib/myStockMaster/tree/master/app/Traits
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait GetModelByUuid
{
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
