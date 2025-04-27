<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */
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
final class WhereFullTextQueryBuilderMixin
{
    public function whereFullText(): \Closure
    {
        /**
         * Add a "where fulltext" clause to the query.
         *
         * @param list<string>|string $columns
         *
         * @return $this
         */
        return function (array|string $columns, string $value, array $options = [], string $boolean = 'and'): self {
            $type = 'Fulltext';

            $columns = (array) $columns;

            $this->wheres[] = ['type' => $type, 'columns' => $columns, 'value' => $value, 'options' => $options, 'boolean' => $boolean];

            $this->addBinding($value);

            return $this;
        };
    }

    public function orWhereFullText(): \Closure
    {
        /**
         * Add an "or where fulltext" clause to the query.
         *
         * @param list<string>|string $columns
         *
         * @return $this
         */
        return fn (array|string $columns, string $value, array $options = []): self => $this->whereFullText($columns, $value, $options, 'or');
    }
}
