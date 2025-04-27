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

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;

/**
 * @mixin \Illuminate\Database\Query\Grammars\MySqlGrammar
 */
#[Mixin(MySqlGrammar::class)]
final class MySqlGrammarMixin
{
    public function whereFullText(): \Closure
    {
        /*
         * Compile a "where fulltext" clause.
         *
         * @param  \Illuminate\Database\Query\Builder  $query
         * @param  array  $where
         * @return string
         */
        return function (Builder $query, array $where): string {
            $columns = $this->columnize($where['columns']);

            $value = $this->parameter($where['value']);

            $mode = 'boolean' === ($where['options']['mode'] ?? [])
            ? ' in boolean mode'
            : ' in natural language mode';

            $expanded = ($where['options']['expanded'] ?? []) && 'boolean' !== ($where['options']['mode'] ?? [])
            ? ' with query expansion'
            : '';

            return "match ($columns) against (".$value."$mode$expanded)";
        };
    }
}
