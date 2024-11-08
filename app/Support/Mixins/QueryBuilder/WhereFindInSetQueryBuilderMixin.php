<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins\QueryBuilder;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\DB;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 */
class WhereFindInSetQueryBuilderMixin
{
    public function whereFindInSet(): callable
    {
        // @var string|Arrayable|string[] $values
        return function (string $column, $values, string $boolean = 'and', bool $not = false): \Illuminate\Database\Query\Builder {
            if (str_contains($column, '.') && ($tablePrefix = DB::getTablePrefix()) && ! str_starts_with($column, $tablePrefix)) {
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
