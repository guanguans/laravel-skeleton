<?php

namespace App\Support\Macros;

use Illuminate\Database\Query\Builder;

class MySqlGrammarMacro
{
    public function whereFulltext(): callable
    {
        /**
         * Compile a "where fulltext" clause.
         *
         * @param  \Illuminate\Database\Query\Builder  $query
         * @param  array  $where
         * @return string
         */
        return function (Builder $query, $where) {
            /** @var \Illuminate\Database\Query\Grammars\MySqlGrammar $this */
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
