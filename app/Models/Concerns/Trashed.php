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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

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
        if (! empty(Request::getFacadeRoot()->get('trashed')) && Request::getFacadeRoot()->get('trashed') === 'with') {
            return $query->withTrashed();
        }

        if (! empty(Request::getFacadeRoot()->get('trashed')) && Request::getFacadeRoot()->get('trashed') === 'only') {
            return $query->onlyTrashed();
        }

        return $query;
    }
}
