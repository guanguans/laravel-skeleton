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

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 * @mixin \Illuminate\Database\Query\Builder
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
