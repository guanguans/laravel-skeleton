<?php

namespace App\Support\Macros;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pipeline\Pipeline;
use InvalidArgumentException;

class QueryBuilderMacro
{
    public function pipe(): callable
    {
        return function (...$pipes) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
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
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->get($columns)->toArray();
        };
    }

    public function firstToArray(): callable
    {
        return function ($columns = ['*']): ?array {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            // return optional($this->first($columns))->toArray();
            return ($model = $this->first($columns)) ? $model->toArray() : (array)$model;
        };
    }

    public function whereFindInSet(): callable
    {
        return function ($column, $value) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->whereRaw("FIND_IN_SET(?, $column)", $value);
        };
    }

    public function orWhereFindInSet(): callable
    {
        return function ($column, $value) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->orWhereRaw("FIND_IN_SET(?, $column)", $value);
        };
    }

    public function orderByWith(): callable
    {
        return function ($relation, $column, $direction = 'asc') {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            if (is_string($relation)) {
                $relation = $this->getRelationWithoutConstraints($relation);
            }

            return $this->orderBy(
                $relation->getRelationExistenceQuery(
                    $relation->getRelated()->newQueryWithoutRelationships(),
                    $this,
                    $column
                ),
                $direction
            );
        };
    }

    public function orderByWithDesc(): callable
    {
        return function ($relation, $column) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->orderByWith($relation, $column, 'desc');
        };
    }

    public function whereLike(): callable
    {
        return function ($column, $value) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->where($column, 'like', "%$value%");
        };
    }

    public function orWhereLike(): callable
    {
        return function ($column, $value) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->orWhere($column, 'like', "%$value%");
        };
    }

    public function whereNotLike(): callable
    {
        return function ($column, $value) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->where($column, 'not like', "%$value%");
        };
    }

    public function orWhereNotLike(): callable
    {
        return function ($column, $value) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->orWhere($column, 'not like', "%$value%");
        };
    }

    public function whereStartsWith(): callable
    {
        return function ($column, $value) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->where($column, 'like', "$value%");
        };
    }

    public function orWhereStartsWith(): callable
    {
        return function ($column, $value) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->orWhere($column, 'like', "$value%");
        };
    }

    public function whereEndsWith(): callable
    {
        return function ($column, $value) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->where($column, 'like', "%$value");
        };
    }

    public function orWhereEndsWith(): callable
    {
        return function ($column, $value) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->orWhere($column, 'like', "%$value");
        };
    }
}
