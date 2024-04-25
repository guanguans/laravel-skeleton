<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Macros\QueryBuilder;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pipeline\Pipeline;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 */
class QueryBuilderMacro
{
    public function pipe(): callable
    {
        return fn (...$pipes) => tap($this, static function ($builder) use ($pipes): void {
            array_unshift($pipes, static function ($builder, $next): void {
                if (
                    ! ($piped = $next($builder)) instanceof EloquentBuilder
                    && ! $piped instanceof QueryBuilder
                    && ! $piped instanceof Relation
                ) {
                    throw new \InvalidArgumentException(
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
    }

    public function getToArray(): callable
    {
        return fn ($columns = ['*']): array => $this->get($columns)->toArray();
    }

    public function firstToArray(): callable
    {
        return fn ($columns = ['*']): ?array =>
            // return optional($this->first($columns))->toArray();
            ($model = $this->first($columns)) ? $model->toArray() : (array) $model;
    }

    /**
     * @see https://github.com/ankane/hightop-php
     */
    public function top(): callable
    {
        return function ($column, ?int $limit = null, ?bool $null = false, ?int $min = null, ?string $distinct = null): array {
            if (null === $distinct) {
                $op = 'count(*)';
            } else {
                $quotedDistinct = $this->getGrammar()->wrap($distinct);
                $op = "count(distinct $quotedDistinct)";
            }

            $relation = $this->select($column)->selectRaw($op)->groupBy($column)->orderByRaw('1 desc')->orderBy($column);

            if (null !== $limit) {
                $relation = $relation->limit($limit);
            }

            if (! $null) {
                $relation = $relation->whereNotNull($column);
            }

            if (null !== $min) {
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
