<?php

/** @noinspection PhpMethodParametersCountMismatchInspection */

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

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 * @mixin \Illuminate\Database\Query\Builder
 */
#[Mixin(EloquentBuilder::class)]
#[Mixin(QueryBuilder::class)]
#[Mixin(RelationBuilder::class)]
class WhereEndsWithQueryBuilderMixin
{
    public function whereEndsWith(): callable
    {
        return function ($column, string $value, string $boolean = 'and', bool $not = false) {
            $operator = $not ? 'not like' : 'like';

            return $this->where($column, $operator, "%$value", $boolean);
        };
    }

    public function whereNotEndsWith(): callable
    {
        return fn ($column, string $value): callable => $this->whereEndsWith($column, $value, 'and', true);
    }

    public function orWhereEndsWith(): callable
    {
        return fn ($column, string $value): callable => $this->whereEndsWith($column, $value, 'or');
    }

    public function orWhereNotEndsWith(): callable
    {
        return fn ($column, string $value): callable => $this->whereEndsWith($column, $value, 'or', true);
    }
}
