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
use Illuminate\Support\Str;

/**
 * @property array $filterable
 * @property mixed $ignoreFilterValue
 *
 * @method static filter(array $input = [])
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Filterable
{
    public function scopeFilter(Builder $query, ?array $input = null): void
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $input = $input ?: Request::getFacadeRoot()->query();

        foreach ($input as $key => $value) {
            if ($value === ($this->ignoreFilterValue ?? 'all')) {
                continue;
            }

            $method = 'filter'.Str::studly($key);
            if (method_exists($this, $method)) {
                \call_user_func([$this, $method], $query, $value, $key);
            } elseif ($this->isFilterable($key)) {
                if (\is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }
    }

    public function isFilterable(string $key): bool
    {
        return property_exists($this, 'filterable') && \in_array($key, $this->filterable, true);
    }

    /**
     * @example
     * <pre>
     *  order_by=id:desc
     *  order_by=age:desc,created_at:asc...
     * </pre>
     */
    public function filterOrderBy(Builder $query, string $value): void
    {
        $segments = explode(',', $value);

        foreach ($segments as $segment) {
            [$key, $direction] = array_pad(explode(':', $segment), 2, 'desc');

            $query->orderBy($key, $direction);
        }
    }
}
