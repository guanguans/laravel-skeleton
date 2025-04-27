<?php

/** @noinspection PhpMethodParametersCountMismatchInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * @mixin \Illuminate\Support\Collection
 */
#[Mixin(Collection::class)]
final class CollectionMixin
{
    /**
     * @noinspection JsonEncodingApiUsageInspection
     * @noinspection PhpMethodParametersCountMismatchInspection
     */
    public static function json(): \Closure
    {
        return static fn (string $json, int $depth = 512, int $options = 0): self => new self(json_decode(
            $json,
            true,
            $depth,
            $options
        ));
    }

    public function pluckToArray(): \Closure
    {
        return fn (null|array|int|string $value, ?string $key = null): array => $this->pluck($value, $key)->toArray();
    }

    public function head(): \Closure
    {
        return fn () => $this->first();
    }

    public function end(): \Closure
    {
        return fn () => $this->last();
    }

    public function after(): \Closure
    {
        return function (mixed $currentItem, mixed $fallback = null) {
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

    public function before(): \Closure
    {
        return fn (mixed $currentItem, mixed $fallback = null) => $this->reverse()->after($currentItem, $fallback);
    }

    public function ifAny(): \Closure
    {
        return function (callable $callback): Collection {
            if (!$this->isEmpty()) {
                $callback($this);
            }

            /** @var \Illuminate\Support\Collection $this */
            return $this;
        };
    }

    public function ifEmpty(): \Closure
    {
        return function (callable $callback): Collection {
            /** @var \Illuminate\Support\Collection $this */
            if ($this->isEmpty()) {
                $callback($this);
            }

            return $this;
        };
    }

    public function if(): \Closure
    {
        return fn (mixed $if, mixed $then = null, mixed $else = null) => value($if, $this) ? value($then, $this) : value($else, $this);
    }

    public function paginate(): \Closure
    {
        return function (int $perPage = 15, string $pageName = 'page', ?int $page = null, ?int $total = null, array $options = []): LengthAwarePaginator {
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

    public function simplePaginate(): \Closure
    {
        return function (int $perPage = 15, string $pageName = 'page', ?int $page = null, array $options = []): Paginator {
            $page = $page ?: Paginator::resolveCurrentPage($pageName);

            $items = $this->slice(($page - 1) * $perPage)->take($perPage + 1);

            $options += [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ];

            return new Paginator($items, $perPage, $page, $options);
        };
    }

    public function filterFilled(): \Closure
    {
        return fn () => $this->filter(static fn (mixed $value) => filled($value));
    }

    public function reduceWithKeys(): \Closure
    {
        return function (callable $callback, mixed $carry = null) {
            /** @var \Illuminate\Support\Collection $this */
            foreach ($this as $key => $value) {
                $carry = $callback($carry, $value, $key);
            }

            return $carry;
        };
    }
}
