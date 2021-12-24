<?php

namespace App\Support\Macros;

use Illuminate\Support\Collection;

class CollectionMacro
{
    public function head(): callable
    {
        return function () {
            /** @var \Illuminate\Support\Collection $this */
            return $this->first();
        };
    }

    public function end(): callable
    {
        return function () {
            /** @var \Illuminate\Support\Collection $this */
            return $this->last();
        };
    }

    public function after(): callable
    {
        return function ($currentItem, $fallback = null) {
            /** @var \Illuminate\Support\Collection $this */
            $currentKey = $this->search($currentItem, true);

            if ($currentKey === false) {
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
        return function ($currentItem, $fallback = null) {
            /** @var \Illuminate\Support\Collection $this */
            return $this->reverse()->after($currentItem, $fallback);
        };
    }

    public function ifAny(): callable
    {
        return function (callable $callback): Collection {
            /** @var \Illuminate\Support\Collection $this */
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
        return function ($if,  $then = null,  $else = null) {
            /** @var \Illuminate\Support\Collection $this */
            return value($if, $this) ? value($then, $this) : value($else, $this);
        };
    }
}
