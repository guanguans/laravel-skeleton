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
class WhereStartsWithQueryBuilderMixin
{
    public function whereStartsWith(): callable
    {
        return function ($column, string $value, string $boolean = 'and', bool $not = false) {
            $operator = $not ? 'not like' : 'like';

            return $this->where($column, $operator, "$value%", $boolean);
        };
    }

    public function whereNotStartsWith(): callable
    {
        return fn ($column, string $value): callable => $this->whereStartsWith($column, $value, 'and', true);
    }

    public function orWhereStartsWith(): callable
    {
        return fn ($column, string $value): callable => $this->whereStartsWith($column, $value, 'or');
    }

    public function orWhereNotStartsWith(): callable
    {
        return fn ($column, string $value): callable => $this->whereStartsWith($column, $value, 'or', true);
    }
}
