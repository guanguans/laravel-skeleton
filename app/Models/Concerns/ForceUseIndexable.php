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
use Illuminate\Support\Facades\DB;

/**
 * @see https://github.com/vpominchuk/laravel-mysql-use-index-scope
 * @see https://github.com/ishaburov/laravel-mysql-index-hints-scope
 * @see https://dev.mysql.com/doc/refman/5.7/en/index-hints.html
 *
 * @method Builder useIndex(string|string[] $index)
 * @method Builder forceIndex(string|string[] $index)
 * @method Builder ignoreIndex(string|string[] $index)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ForceUseIndexable
{
    /** @var list<string> */
    protected array $from = [];

    /**
     * @param list<string>|string $index
     */
    protected function scopeUseIndex(Builder $query, array|string $index): Builder
    {
        $table = $this->getTable();

        $index = $this->parseIndexName($index);

        $this->from[] = "USE INDEX($index)";

        $raw = "`$table` ".implode(' ', $this->from);

        // @var Builder $query
        return $query->from(DB::raw($raw));
    }

    /**
     * @param list<string>|string $index
     */
    protected function scopeForceIndex(Builder $query, array|string $index): Builder
    {
        $table = $this->getTable();

        $index = $this->parseIndexName($index);

        $this->from[] = "FORCE INDEX($index)";

        $raw = "`$table` ".implode(' ', $this->from);

        // @var Builder $query
        return $query->from(DB::raw($raw));
    }

    /**
     * @param list<string>|string $index
     */
    protected function scopeIgnoreIndex(Builder $query, array|string $index): Builder
    {
        $table = $this->getTable();

        $index = $this->parseIndexName($index);

        $this->from[] = "IGNORE INDEX($index)";

        $raw = "`$table` ".implode(' ', $this->from);

        // @var Builder $query
        return $query->from(DB::raw($raw));
    }

    /**
     * @param list<string>|string $index
     */
    protected function parseIndexName(array|string $index): string
    {
        if (\is_array($index)) {
            return '`'.implode('`, `', $index).'`';
        }

        return '`'.$index.'`';
    }
}
