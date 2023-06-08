<?php

declare(strict_types=1);

namespace App\Macros\QueryBuilder;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 */
class WhereLikeQueryBuilderMacro
{
    public function whereLike(): callable
    {
        return function ($column, string $value, string $boolean = 'and', bool $not = false) {
            $operator = $not ? 'not like' : 'like';

            return $this->where($column, $operator, "%$value%", $boolean);
        };
    }

    public function whereNotLike(): callable
    {
        return fn ($column, string $value) => $this->whereLike($column, $value, 'and', true);
    }

    public function orWhereLike(): callable
    {
        return fn ($column, string $value) => $this->whereLike($column, $value, 'or');
    }

    public function orWhereNotLike(): callable
    {
        return fn ($column, string $value) => $this->whereLike($column, $value, 'or', true);
    }
}
