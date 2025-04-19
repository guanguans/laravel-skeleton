<?php

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
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation as RelationBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pipeline\Pipeline;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 * @mixin \Illuminate\Database\Query\Builder
 */
#[Mixin(EloquentBuilder::class)]
#[Mixin(QueryBuilder::class)]
#[Mixin(RelationBuilder::class)]
class QueryBuilderMixin
{
    public function pipe(): callable
    {
        return fn (...$pipes) => tap($this, static function ($builder) use ($pipes): void {
            array_unshift($pipes, static function ($builder, $next): void {
                throw_if(!($piped = $next($builder)) instanceof EloquentBuilder
                && !$piped instanceof QueryBuilder
                && !$piped instanceof RelationBuilder, \InvalidArgumentException::class, \sprintf(
                    'Query builder pipeline must be return a %s or %s or %s instance.',
                    EloquentBuilder::class,
                    QueryBuilder::class,
                    RelationBuilder::class,
                ));
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
        // return optional($this->first($columns))->toArray();
        return fn ($columns = ['*']): ?array => ($model = $this->first($columns)) ? $model->toArray() : (array) $model;
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

            $relation = $this->select($column)->selectRaw($op)->groupBy($column)->orderByRaw('1 desc')->oldest($column);

            if (null !== $limit) {
                $relation = $relation->limit($limit);
            }

            if (!$null) {
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

                $result[$values[0]] = $values[1];
            }

            return $result;
        };
    }
}
