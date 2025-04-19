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
class WhereFindInSetQueryBuilderMixin
{
    public function whereFindInSet(): callable
    {
        // @var string|Arrayable|string[] $values
        return function (string $column, $values, string $boolean = 'and', bool $not = false) {
            if (str_contains($column, '.') && ($tablePrefix = DB::getTablePrefix()) && !str_starts_with($column, $tablePrefix)) {
                $column = $tablePrefix.$column;
            }

            $sql = $not ? "not find_in_set(?, $column)" : "find_in_set(?, $column)";

            $values instanceof Arrayable and $values = $values->toArray();
            \is_array($values) and $values = implode(',', $values);

            return $this->whereRaw($sql, $values, $boolean);
        };
    }

    public function whereNotFindInSet(): callable
    {
        // @var string|Arrayable|string[] $values
        return fn (string $column, $values): callable => $this->whereFindInSet($column, $values, 'and', true);
    }

    public function orWhereFindInSet(): callable
    {
        // @var string|Arrayable|string[] $values
        return fn (string $column, $values): callable => $this->whereFindInSet($column, $values, 'or');
    }

    public function orWhereNotFindInSet(): callable
    {
        // @var string|Arrayable|string[] $values
        return fn (string $column, $values): callable => $this->whereFindInSet($column, $values, 'or', true);
    }
}
