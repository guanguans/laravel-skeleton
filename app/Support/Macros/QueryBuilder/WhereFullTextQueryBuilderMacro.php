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
class WhereFullTextQueryBuilderMacro
{
    public function whereFullText(): callable
    {
        /*
         * Add a "where fulltext" clause to the query.
         *
         * @param  string|string[]  $columns
         * @param  string  $value
         * @param  string  $boolean
         * @return $this
         */
        return function ($columns, $value, array $options = [], $boolean = 'and') {
            $type = 'Fulltext';

            $columns = (array) $columns;

            $this->wheres[] = ['type' => $type, 'columns' => $columns, 'value' => $value, 'options' => $options, 'boolean' => $boolean];

            $this->addBinding($value);

            return $this;
        };
    }

    public function orWhereFullText(): callable
    {
        /*
         * Add a "or where fulltext" clause to the query.
         *
         * @param  string|string[]  $columns
         * @param  string  $value
         * @return $this|callable
         */
        return fn ($columns, $value, array $options = []): callable => $this->whereFulltext($columns, $value, $options, 'or');
    }
}
