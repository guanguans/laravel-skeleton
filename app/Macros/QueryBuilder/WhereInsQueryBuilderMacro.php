<?php

declare(strict_types=1);

namespace App\Macros\QueryBuilder;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\DB;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 */
class WhereInsQueryBuilderMacro
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
                        sprintf('The value of the column is not found in the array.: %s', $column),
                        E_USER_ERROR
                    );

                    return $sortedValue;
                }, []);
            }, $values);

            $rawValue = sprintf('(%s)', implode(',', array_fill(0, \count($columns), '?')));
            $rawValues = implode(',', array_fill(0, \count($values), $rawValue));

            $raw = "($rawColumns) $operator ($rawValues)";

            return $this->whereRaw($raw, $values, $boolean);
        };
    }

    public function whereNotIns(): callable
    {
        return fn (array $columns, $values) => $this->whereIns($columns, $values, 'and', true);
    }

    public function orWhereIns(): callable
    {
        return fn (array $columns, $values) => $this->whereIns($columns, $values, 'or');
    }

    public function orWhereNotIns(): callable
    {
        return fn (array $columns, $values) => $this->whereIns($columns, $values, 'or', true);
    }
}
