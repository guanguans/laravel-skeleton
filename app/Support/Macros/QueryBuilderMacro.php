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
}
