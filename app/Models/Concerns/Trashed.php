<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * @see https://github.com/Zakarialabib/myStockMaster/tree/master/app/Traits
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Trashed
{
    /**
     * Scope a query to get with transed or only transhed resource.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function scopeTrashed(Builder $query): Builder
    {
        if (! empty(request()->get('trashed')) && request()->get('trashed') === 'with') {
            return $query->withTrashed();
        }

        if (! empty(request()->get('trashed')) && request()->get('trashed') === 'only') {
            return $query->onlyTrashed();
        }

        return $query;
    }
}
