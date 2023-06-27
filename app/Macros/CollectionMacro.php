<?php

declare(strict_types=1);

namespace App\Macros;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * @mixin \Illuminate\Support\Collection
 */
class CollectionMacro
{
    public function pluckToArray(): callable
    {
        return fn ($value, $key = null): array => $this->pluck($value, $key)->toArray();
    }

    public function head(): callable
    {
        return fn () => $this->first();
    }

    public function end(): callable
    {
        return fn () => $this->last();
    }

    public function after(): callable
    {
        return function ($currentItem, $fallback = null) {
            $currentKey = $this->search($currentItem, true);

            if (false === $currentKey) {
                return $fallback;
            }

            $currentOffset = $this->keys()->search($currentKey, true);

            $next = $this->slice($currentOffset, 2);

            if ($next->count() < 2) {
                return $fallback;
            }

            return $next->last();
        };
    }

    public function before(): callable
    {
        return fn ($currentItem, $fallback = null) => $this->reverse()->after($currentItem, $fallback);
    }

    public function ifAny(): callable
    {
        return function (callable $callback): Collection {
            if (! $this->isEmpty()) {
                $callback($this);
            }

            return $this;
        };
    }

    public function ifEmpty(): callable
    {
        return function (callable $callback): Collection {
            /** @var \Illuminate\Support\Collection $this */
            if ($this->isEmpty()) {
                $callback($this);
            }

            return $this;
        };
    }

    public function if(): callable
    {
        return fn ($if, $then = null, $else = null) => value($if, $this) ? value($then, $this) : value($else, $this);
    }

    public function paginate(): callable
    {
        return function ($perPage = 15, $pageName = 'page', $page = null, $total = null, $options = []) {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            $items = $this->forPage($page, $perPage)->values();

            $total = $total ?: $this->count();

            $options += [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ];

            return new LengthAwarePaginator($items, $total, $perPage, $page, $options);
        };
    }

    public function simplePaginate(): callable
    {
        return function ($perPage = 15, $pageName = 'page', $page = null, $options = []) {
            $page = $page ?: Paginator::resolveCurrentPage($pageName);

            $items = $this->slice(($page - 1) * $perPage)->take($perPage + 1);

            $options += [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ];

            return new Paginator($items, $perPage, $page, $options);
        };
    }

    public function filterFilled(): callable
    {
        return fn () => $this->filter(static fn ($value) => filled($value));
    }

    public function reduces(): callable
    {
        return function (callable $callback, $carry = null) {
            foreach ($this as $key => $value) {
                $carry = $callback($carry, $value, $key);
            }

            return $carry;
        };
    }

    public function maps(): callable
    {
        return function (callable $callback) {
            $arr = [];
            foreach ($this as $key => $value) {
                $arr[$key] = $callback($value, $key);
            }

            return $arr;
        };
    }
}
