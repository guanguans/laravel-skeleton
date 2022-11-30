<?php

namespace App\Macros\QueryBuilder;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pipeline\Pipeline;
use InvalidArgumentException;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 */
class QueryBuilderMacro
{
    public function pipe(): callable
    {
        return function (...$pipes) {
            return tap($this, function ($builder) use ($pipes) {
                array_unshift($pipes, function ($builder, $next) {
                    if (
                        ! ($piped = $next($builder)) instanceof EloquentBuilder
                        && ! $piped instanceof QueryBuilder
                        && ! $piped instanceof Relation
                    ) {
                        throw new InvalidArgumentException(
                            sprintf(
                                'Query builder pipeline must be return a %s or %s or %s instance.',
                                EloquentBuilder::class,
                                QueryBuilder::class,
                                Relation::class,
                            )
                        );
                    }
                });

                (new Pipeline(app()))
                    ->send($builder)
                    ->through(...$pipes)
                    ->thenReturn();
            });
        };
    }

    public function getToArray(): callable
    {
        return function ($columns = ['*']): array {
            return $this->get($columns)->toArray();
        };
    }

    public function firstToArray(): callable
    {
        return function ($columns = ['*']): ?array {
            // return optional($this->first($columns))->toArray();
            return ($model = $this->first($columns)) ? $model->toArray() : (array) $model;
        };
    }

    /**
     * @see https://github.com/ankane/hightop-php
     */
    public function top(): callable
    {
        return function ($column, ?int $limit = null, ?bool $null = false, ?int $min = null, ?string $distinct = null) {
            if ($distinct === null) {
                $op = 'count(*)';
            } else {
                $quotedDistinct = $this->getGrammar()->wrap($distinct);
                $op = "count(distinct $quotedDistinct)";
            }

            $relation = $this->select($column)->selectRaw($op)->groupBy($column)->orderByRaw('1 desc')->orderBy($column);

            if ($limit !== null) {
                $relation = $relation->limit($limit);
            }

            if (! $null) {
                $relation = $relation->whereNotNull($column);
            }

            if ($min !== null) {
                $relation = $relation->havingRaw("$op >= ?", [$min]);
            }

            // can't use pluck with expressions in Postgres without an alias
            $rows = $relation->get()->toArray();
            $result = [];
            foreach ($rows as $row) {
                $values = array_values($row);
                /** @noinspection OffsetOperationsInspection */
                $result[$values[0]] = $values[1];
            }

            return $result;
        };
    }
}
