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
     *
     * @see \Illuminate\Support\Traits\EnumeratesValues::fromJson()
     */
    public static function fromJson(): \Closure
    {
        return static fn (
            string $json,
            int $depth = 512,
            int $flags = 0
        ): self => new self(json_decode($json, true, $depth, $flags));
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

    public function if(): \Closure
    {
        return fn (mixed $if, mixed $then = null, mixed $else = null) => value($if, $this) ? value($then, $this) : value($else, $this);
    }

    /**
     * @see https://github.com/spatie/laravel-collection-macros/blob/main/src/Macros/Paginate.php
     */
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

    /**
     * @see https://github.com/spatie/laravel-collection-macros/blob/main/src/Macros/SimplePaginate.php
     */
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
}
