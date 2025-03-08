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

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * @method static Builder|Model forceIndex(array|string[] $indexes, string $for = '', string $as = '')
 * @method static Builder|Model useIndex(string|string[] $indexes, string $for = '', string $as = '')
 * @method static Builder|Model ignoreIndex(array|string[] $indexes, string $for = '', string $as = '')
 * @method static Builder|Model getTable()
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @see https://dev.mysql.com/doc/refman/5.7/en/index-hints.html
 */
trait IndexHintsable
{
    protected $forceIndexes = [];
    protected $useIndexes = [];
    protected $ignoreIndexes = [];
    protected $preparedIndexes = '';

    /**
     * @param list<string>|string $indexes
     * @param string $for JOIN|ORDER BY|GROUP BY
     */
    public function scopeForceIndex(Builder $query, array|string $indexes, string $for = '', string $as = ''): Builder
    {
        throw_if(Str::contains($this->preparedIndexes, 'USE'), \InvalidArgumentException::class, 'It is an error to mix USE INDEX and FORCE INDEX for the same table.');

        if (!$this->tableIndexExists($indexes, 'force')) {
            return $query;
        }

        $this->setTableNameAndAlias($as);

        $indexesToSting = implode(',', $this->forceIndexes);
        $this->forceIndexes = [];
        $this->preparedIndexes .= ' FORCE INDEX';
        $this->prepareFor($for);
        $this->preparedIndexes .= " ($indexesToSting)";

        return $query->from(DB::raw($this->preparedIndexes));
    }

    /**
     * @param list<string>|string $indexes
     */
    public function scopeUseIndex(Builder $query, array|string $indexes, string $for = '', string $as = ''): Builder
    {
        throw_if(Str::contains($this->preparedIndexes, 'FORCE'), \Exception::class, 'However, it is an error to mix USE INDEX and FORCE INDEX for the same table:');

        if (!$this->tableIndexExists($indexes, 'use')) {
            return $query;
        }

        $this->setTableNameAndAlias($as);

        $indexesToSting = implode(',', $this->useIndexes);
        $this->useIndexes = [];
        $this->preparedIndexes .= ' USE INDEX';
        $this->prepareFor($for);
        $this->preparedIndexes .= " ($indexesToSting)";

        return $query->from(DB::raw($this->preparedIndexes));
    }

    /**
     * @param list<string>|string $indexes
     */
    public function scopeIgnoreIndex(Builder $query, array|string $indexes, string $for = '', string $as = ''): Builder
    {
        if (!$this->tableIndexExists($indexes, 'ignore')) {
            return $query;
        }

        $this->setTableNameAndAlias($as);

        $indexesToSting = implode(',', $this->ignoreIndexes);
        $this->ignoreIndexes = [];
        $this->preparedIndexes .= ' IGNORE INDEX';
        $this->prepareFor($for);
        $this->preparedIndexes .= " ($indexesToSting)";

        return $query->from(DB::raw($this->preparedIndexes));
    }

    protected function setTableNameAndAlias(string $as = ''): void
    {
        if (!empty($this->preparedIndexes)) {
            return;
        }

        $this->preparedIndexes = self::getTable();
        $this->preparedIndexes .= empty($as) ? '' : " {$as}";
    }

    /**
     * @param list<string>|string $indexes
     */
    protected function tableIndexExists(array|string $indexes, string $type): bool
    {
        foreach (Arr::wrap($indexes) as $index) {
            $index = strtolower($index);

            /** @noinspection PhpVoidFuqnctionResultUsedInspection */
            Schema::table(self::getTable(), fn (Blueprint $table) => $this->fillIndexes($table, $index, $type));
        }

        return !empty($this->forceIndexes) || !empty($this->ignoreIndexes) || !empty($this->useIndexes);
    }

    protected function fillIndexes(Blueprint $table, string $index, string $type): void
    {
        if (!$table->hasIndex($index)) {
            return;
        }

        switch ($type) {
            case 'force':
                $this->forceIndexes[] = $index;

                break;
            case 'ignore':
                $this->ignoreIndexes[] = $index;

                break;
            case 'use':
                $this->useIndexes[] = $index;

                break;
        }
    }

    protected function prepareFor(string $for = ''): bool
    {
        if (empty($for)) {
            return false;
        }

        $for = strtoupper(str_replace('_', ' ', $for));
        $this->preparedIndexes .= " FOR $for";

        return true;
    }
}
