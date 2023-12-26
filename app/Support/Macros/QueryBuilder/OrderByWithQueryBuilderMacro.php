<?php

declare(strict_types=1);

namespace App\Support\Macros\QueryBuilder;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 */
class OrderByWithQueryBuilderMacro
{
    public function orderByWith(): callable
    {
        return function ($relation, $column, $direction = 'asc') {
            if (\is_string($relation)) {
                $relation = $this->getRelationWithoutConstraints($relation);
            }

            /** @noinspection PhpParamsInspection */
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
        return fn ($relation, $column) => $this->orderByWith($relation, $column, 'desc');
    }
}
