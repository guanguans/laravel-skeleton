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
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation as RelationBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Cache;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 * @mixin \Illuminate\Database\Query\Builder
 */
#[Mixin(EloquentBuilder::class)]
#[Mixin(QueryBuilder::class)]
#[Mixin(RelationBuilder::class)]
final class QueryBuilderMixin
{
    /**
     * @see https://medium.com/@developerawam/laravel-too-slow-just-add-these-few-lines-of-cache-cf2893e50eef
     */
    public function cache(): \Closure
    {
        /**
         * @noinspection RedundantDocCommentTagInspection
         *
         * @param \Closure(EloquentBuilder|QueryBuilder|RelationBuilder|self): mixed $callback
         */
        return fn (
            string $key,
            null|\Closure|\DateInterval|\DateTimeInterface|int $ttl,
            \Closure $callback,
            ?string $driver = null
        ): mixed => Cache::memo($driver)->remember($key, $ttl, fn (): mixed => $callback($this));
    }

    public function pipe(): \Closure
    {
        return fn (mixed ...$pipes) => tap(
            $this,
            static function (EloquentBuilder|QueryBuilder|RelationBuilder $builder) use ($pipes): void {
                array_unshift(
                    $pipes,
                    static function (EloquentBuilder|QueryBuilder|RelationBuilder $builder, \Closure $next): void {
                        throw_if(
                            !($piped = $next($builder)) instanceof EloquentBuilder
                            && !$piped instanceof QueryBuilder
                            && !$piped instanceof RelationBuilder,
                            \InvalidArgumentException::class,
                            \sprintf(
                                'Query builder pipeline must be return a %s or %s or %s instance.',
                                EloquentBuilder::class,
                                QueryBuilder::class,
                                RelationBuilder::class,
                            )
                        );
                    }
                );

                (new Pipeline(app()))
                    ->send($builder)
                    ->through(...$pipes)
                    ->thenReturn();
            }
        );
    }

    public function getToArray(): \Closure
    {
        return fn (array|string $columns = ['*']): array => $this->get($columns)->toArray();
    }

    public function firstToArray(): \Closure
    {
        return fn (array|string $columns = ['*']): ?array => $this->first($columns)?->toArray();
    }

    /**
     * @see https://github.com/ankane/hightop-php
     */
    public function top(): \Closure
    {
        return function (Expression|string $column, ?int $limit = null, ?bool $null = false, ?int $min = null, ?string $distinct = null): array {
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
