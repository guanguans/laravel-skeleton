<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Macros\QueryBuilder;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 */
class WhereStartsWithQueryBuilderMacro
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
        return fn ($column, string $value) => $this->whereStartsWith($column, $value, 'and', true);
    }

    public function orWhereStartsWith(): callable
    {
        return fn ($column, string $value) => $this->whereStartsWith($column, $value, 'or');
    }

    public function orWhereNotStartsWith(): callable
    {
        return fn ($column, string $value) => $this->whereStartsWith($column, $value, 'or', true);
    }
}
