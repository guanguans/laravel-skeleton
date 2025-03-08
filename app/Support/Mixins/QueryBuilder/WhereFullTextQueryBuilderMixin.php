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
class WhereFullTextQueryBuilderMixin
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
        return function ($columns, $value, array $options = [], $boolean = 'and'): static {
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
