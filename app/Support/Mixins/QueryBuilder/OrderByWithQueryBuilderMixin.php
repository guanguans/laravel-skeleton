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

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 */
#[Mixin(\Illuminate\Database\Eloquent\Builder::class)]
#[Mixin(\Illuminate\Database\Query\Builder::class)]
#[Mixin(\Illuminate\Database\Eloquent\Relations\Relation::class)]
class OrderByWithQueryBuilderMixin
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
        return fn ($relation, $column): callable => $this->orderByWith($relation, $column, 'desc');
    }
}
