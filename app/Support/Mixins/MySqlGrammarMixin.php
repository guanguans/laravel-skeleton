<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;

/**
 * @mixin \Illuminate\Database\Query\Grammars\MySqlGrammar
 */
#[Mixin(MySqlGrammar::class)]
class MySqlGrammarMixin
{
    public function whereFulltext(): callable
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

            $mode = ($where['options']['mode'] ?? []) === 'boolean'
            ? ' in boolean mode'
            : ' in natural language mode';

            $expanded = ($where['options']['expanded'] ?? []) && ($where['options']['mode'] ?? []) !== 'boolean'
            ? ' with query expansion'
            : '';

            return "match ({$columns}) against (".$value."{$mode}{$expanded})";
        };
    }
}
