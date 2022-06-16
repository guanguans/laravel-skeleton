<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @method static \Illuminate\Database\Eloquent\Builder filter(string $name, $default = null, ?string $internalName = null, $ignore = [])
 * @method static \Illuminate\Database\Eloquent\Builder exactFilter(string $name, $default = null, ?string $internalName = null, $ignore = [])
 * @method static \Illuminate\Database\Eloquent\Builder partialFilter(string $name, $default = null, ?string $internalName = null, $ignore = [])
 * @method static \Illuminate\Database\Eloquent\Builder scopeFilter(string $name, $default = null, ?string $internalName = null, $ignore = [])
 * @method static \Illuminate\Database\Eloquent\Builder callbackFilter(string $name, callable $callback)
 * @method static \Illuminate\Database\Eloquent\Builder trashedFilter(string $name = 'trashed')
 */
trait RequestFilterable
{
    public function scopeFilter(Builder $query, string $name, $default = null, ?string $internalName = null, $ignore = [])
    {
        return $this->scopeExactFilter($query, $name, $default, $internalName, $ignore);
    }

    public function scopeExactFilter(Builder $query, string $name, $default = null, ?string $internalName = null, $ignore = [])
    {
        if (
            (request()->has($name) || $default !== null) &&
            ! in_array($value = request()->input($name, $default), Arr::wrap($ignore))
        ) {
            if (is_array($value)) {
                return $query->whereIn($query->qualifyColumn($internalName ?: $name), $value);
            }

            return $query->where($query->qualifyColumn($internalName ?: $name), '=', $value);
        }

        return $query;
    }

    public function scopePartialFilter(Builder $query, string $name, $default = null, ?string $internalName = null, $ignore = [])
    {
        if (
            (request()->has($name) || $default !== null) &&
            ! in_array($value = request()->input($name, $default), Arr::wrap($ignore))
        ) {
            $wrappedProperty = $query->getQuery()->getGrammar()->wrap($query->qualifyColumn($internalName ?: $name));

            $sql = "LOWER({$wrappedProperty}) LIKE ?";

            if (is_array($value)) {
                if (count(array_filter($value, 'strlen')) === 0) {
                    return $query;
                }

                $query->where(function (Builder $query) use ($value, $sql) {
                    foreach (array_filter($value, 'strlen') as $partialValue) {
                        $partialValue = mb_strtolower($partialValue, 'UTF8');

                        $query->orWhereRaw($sql, ["%{$partialValue}%"]);
                    }
                });

                return $query;
            }

            $value = mb_strtolower($value, 'UTF8');

            $query->whereRaw($sql, ["%{$value}%"]);
        }

        return $query;
    }

    public function scopeScopeFilter(Builder $query, string $name, $default = null, ?string $internalName = null, $ignore = []): Builder
    {
        if (
            (request()->has($name) || $default !== null) &&
            ! in_array($value = request()->input($name, $default), Arr::wrap($ignore))
        ) {
            $nameParts = collect(explode('.', $internalName ?: $name));

            $scope = Str::camel($nameParts->pop()); // TODO: Make this configurable?

            $relation = $nameParts->implode('.');

            if ($relation) {
                return $query->whereHas($relation, function (Builder $query) use ($scope, $value) {
                    return $query->$scope($value);
                });
            }

            return $query->$scope($value);
        }

        return $query;
    }

    public function scopeCallbackFilter(Builder $query, string $name, callable $callback)
    {
        if (request()->has($name)) {
            return call_user_func($callback, $query, request()->input($name), $name);
        }

        return $query;
    }

    public function scopeTrashedFilter(Builder $query, string $name = 'trashed')
    {
        if (request()->has($name)) {
            if (($value = request()->input($name)) === 'with') {
                return $query->withTrashed();
            }

            if ($value === 'only') {
                return $query->onlyTrashed();
            }

            $query->withoutTrashed();
        }

        return $query;
    }
}
