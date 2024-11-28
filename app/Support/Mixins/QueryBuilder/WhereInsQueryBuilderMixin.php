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

use App\Support\Attributes\Mixin;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\DB;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 */
#[Mixin(\Illuminate\Database\Eloquent\Builder::class)]
#[Mixin(\Illuminate\Database\Query\Builder::class)]
#[Mixin(\Illuminate\Database\Eloquent\Relations\Relation::class)]
class WhereInsQueryBuilderMixin
{
    public function whereIns(): callable
    {
        // @var Arrayable|array[] $values
        return function (array $columns, $values, string $boolean = 'and', bool $not = false) {
            $operator = $not ? 'not in' : 'in';

            $sterilizedColumns = array_map(static function (string $column): string {
                if (str_contains($column, '.') && ($tablePrefix = DB::getTablePrefix()) && ! str_starts_with($column, $tablePrefix)) {
                    $column = $tablePrefix.$column;
                }

                return $column;
            }, $columns);
            $rawColumns = implode(',', $sterilizedColumns);

            $values instanceof Arrayable and $values = $values->toArray();
            $values = array_map(static function ($value) use ($columns) {
                if (array_is_list($value)) {
                    return $value;
                }

                return array_reduce($columns, static function ($sortedValue, $column) use ($value) {
                    $sortedValue[$column] = $value[$column] ?? trigger_error(
                        \sprintf('The value of the column is not found in the array.: %s', $column),
                        E_USER_ERROR
                    );

                    return $sortedValue;
                }, []);
            }, $values);

            $rawValue = \sprintf('(%s)', implode(',', array_fill(0, \count($columns), '?')));
            $rawValues = implode(',', array_fill(0, \count($values), $rawValue));

            $raw = "($rawColumns) $operator ($rawValues)";

            return $this->whereRaw($raw, $values, $boolean);
        };
    }

    public function whereNotIns(): callable
    {
        return fn (array $columns, $values): callable => $this->whereIns($columns, $values, 'and', true);
    }

    public function orWhereIns(): callable
    {
        return fn (array $columns, $values): callable => $this->whereIns($columns, $values, 'or');
    }

    public function orWhereNotIns(): callable
    {
        return fn (array $columns, $values): callable => $this->whereIns($columns, $values, 'or', true);
    }
}
