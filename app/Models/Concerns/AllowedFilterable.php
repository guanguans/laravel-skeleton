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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

/**
 * @method static Builder allowedFilter(string $name, mixed $default = null, ?string $internalName = null, mixed $ignore = null)
 * @method static Builder allowedExactFilter(string $name, mixed $default = null, ?string $internalName = null, mixed $ignore = null)
 * @method static Builder allowedPartialFilter(string $name, mixed $default = null, ?string $internalName = null, mixed $ignore = null)
 * @method static Builder allowedScopeFilter(string $name, mixed $default = null, ?string $internalName = null, mixed $ignore = null)
 * @method static Builder allowedCallbackFilter(string $name, callable $callback)
 * @method static Builder allowedTrashedFilter(string $name = 'trashed')
 * @method static Builder allowedSorts(array $allowedSorts, array $default = [], string $name = 'sorts')
 * @method static Builder allowedSort(string $name, mixed $default = null, ?string $internalName = null)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait AllowedFilterable
{
    public function scopeAllowedFilter(
        Builder $query,
        string $name,
        mixed $default = null,
        ?string $internalName = null,
        mixed $ignore = null
    ): Builder {
        return $this->scopeAllowedExactFilter($query, $name, $default, $internalName, $ignore);
    }

    public function scopeAllowedExactFilter(
        Builder $query,
        string $name,
        mixed $default = null,
        ?string $internalName = null,
        mixed $ignore = null
    ): Builder {
        if (
            (Request::getFacadeRoot()->has($name) || null !== $default)
            && !\in_array($value = Request::getFacadeRoot()->input($name, $default), Arr::wrap($ignore), true)
        ) {
            if (\is_array($value)) {
                return $query->whereIn($query->qualifyColumn($internalName ?: $name), $value);
            }

            return $query->where($query->qualifyColumn($internalName ?: $name), '=', $value);
        }

        return $query;
    }

    public function scopeAllowedPartialFilter(
        Builder $query,
        string $name,
        mixed $default = null,
        ?string $internalName = null,
        mixed $ignore = null
    ): Builder {
        if (
            (Request::getFacadeRoot()->has($name) || null !== $default)
            && !\in_array($value = Request::getFacadeRoot()->input($name, $default), Arr::wrap($ignore), true)
        ) {
            $wrappedProperty = $query->getQuery()->getGrammar()->wrap($query->qualifyColumn($internalName ?: $name));

            $sql = "LOWER($wrappedProperty) LIKE ?";

            if (\is_array($value)) {
                if ([] === array_filter($value, strlen(...))) {
                    return $query;
                }

                $query->where(static function (Builder $query) use ($value, $sql): void {
                    foreach (array_filter($value, strlen(...)) as $partialValue) {
                        $partialValue = mb_strtolower($partialValue, 'UTF8');

                        $query->orWhereRaw($sql, ["%$partialValue%"]);
                    }
                });

                return $query;
            }

            $value = mb_strtolower($value, 'UTF8');

            $query->whereRaw($sql, ["%$value%"]);
        }

        return $query;
    }

    public function scopeAllowedScopeFilter(
        Builder $query,
        string $name,
        mixed $default = null,
        ?string $internalName = null,
        mixed $ignore = null
    ): Builder {
        if (
            (Request::getFacadeRoot()->has($name) || null !== $default)
            && !\in_array($value = Request::getFacadeRoot()->input($name, $default), Arr::wrap($ignore), true)
        ) {
            $nameParts = collect(explode('.', $internalName ?: $name));

            $scope = Str::camel($nameParts->pop()); // TODO: Make this configurable?

            $relation = $nameParts->implode('.');

            if ($relation) {
                return $query->whereHas($relation, static fn (Builder $query) => $query->{$scope}($value));
            }

            return $query->{$scope}($value);
        }

        return $query;
    }

    public function scopeAllowedCallbackFilter(Builder $query, string $name, callable $callback): Builder
    {
        if (Request::getFacadeRoot()->has($name)) {
            return $callback($query, Request::getFacadeRoot()->input($name), $name);
        }

        return $query;
    }

    /**
     * @noinspection PhpStaticAsDynamicMethodCallInspection
     */
    public function scopeAllowedTrashedFilter(Builder $query, string $name = 'trashed'): Builder
    {
        if (Request::getFacadeRoot()->has($name)) {
            if ('with' === ($value = Request::getFacadeRoot()->input($name))) {
                return $query->withTrashed();
            }

            if ('only' === $value) {
                return $query->onlyTrashed();
            }

            $query->withoutTrashed();
        }

        return $query;
    }

    /**
     * @example
     *
     * ```
     * sorts[id]:desc
     * sorts[]:-updated_at
     * sorts[created_at]:asc
     * ```
     */
    public function scopeAllowedSorts(Builder $query, array $allowedSorts, array $default = [], string $name = 'sorts'): Builder
    {
        $sorts = Request::getFacadeRoot()->input($name, $default);

        foreach ($sorts as $direction => $column) {
            if (\is_int($column)) {
                '-' === $direction[0]
                ? ($column = ltrim($direction, '-') and $direction = 'desc')
                : ($column = $direction and $direction = 'asc');
            }

            if (!\in_array($column, $allowedSorts, true)) {
                continue;
            }

            $query->orderBy($query->qualifyColumn($column), $direction);
        }

        return $query;
    }

    public function scopeAllowedSort(Builder $query, string $name, mixed $default = null, ?string $internalName = null): Builder
    {
        if (null !== $default || Request::getFacadeRoot()->hasAny([$name, '-'.$name])) {
            $column = $internalName ?: $name;

            if (Request::getFacadeRoot()->has('-'.$name)) {
                $direction = 'desc';
            } elseif (Request::getFacadeRoot()->has($name)) {
                $direction = 'asc';
            } else {
                $direction = $default;
            }

            $query->orderBy($query->qualifyColumn($column), $direction);
        }

        return $query;
    }
}
