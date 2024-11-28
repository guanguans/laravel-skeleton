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
