<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */
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

namespace App\Support\Mixins\QueryBuilder;

use App\Support\Attributes\Mixin;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation as RelationBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 * @mixin \Illuminate\Database\Query\Builder
 */
#[Mixin(EloquentBuilder::class)]
#[Mixin(QueryBuilder::class)]
#[Mixin(RelationBuilder::class)]
final class WhereFindInSetQueryBuilderMixin
{
    public function whereFindInSet(): \Closure
    {
        return function (string $column, array|Arrayable|string $values, string $boolean = 'and', bool $not = false): self {
            if (str_contains($column, '.') && ($tablePrefix = DB::getTablePrefix()) && !str_starts_with($column, $tablePrefix)) {
                $column = $tablePrefix.$column;
            }

            $sql = $not ? "not find_in_set(?, $column)" : "find_in_set(?, $column)";

            $values instanceof Arrayable and $values = $values->toArray();
            \is_array($values) and $values = implode(',', $values);

            return $this->whereRaw($sql, $values, $boolean);
        };
    }

    public function whereNotFindInSet(): \Closure
    {
        return fn (string $column, array|Arrayable|string $values): self => $this->whereFindInSet($column, $values, 'and', true);
    }

    public function orWhereFindInSet(): \Closure
    {
        return fn (string $column, array|Arrayable|string $values): self => $this->whereFindInSet($column, $values, 'or');
    }

    public function orWhereNotFindInSet(): \Closure
    {
        return fn (string $column, array|Arrayable|string $values): self => $this->whereFindInSet($column, $values, 'or', true);
    }
}
