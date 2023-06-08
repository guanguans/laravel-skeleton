<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @method Builder useIndex(string|string[] $index)
 * @method Builder forceIndex(string|string[] $index)
 * @method Builder ignoreIndex(string|string[] $index)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ForceUseIndexable
{
    protected $from = [];

    /**
     * @param  string|string[]  $index
     */
    public function scopeUseIndex(Builder $query, $index): Builder
    {
        $table = $this->getTable();

        $index = $this->parseIndexName($index);

        $this->from[] = "USE INDEX($index)";

        $raw = "`$table` ".implode(' ', $this->from);

        // @var Builder $query
        return $query->from(DB::raw($raw));
    }

    /**
     * @param  string|string[]  $index
     */
    public function scopeForceIndex(Builder $query, $index): Builder
    {
        $table = $this->getTable();

        $index = $this->parseIndexName($index);

        $this->from[] = "FORCE INDEX($index)";

        $raw = "`$table` ".implode(' ', $this->from);

        // @var Builder $query
        return $query->from(DB::raw($raw));
    }

    /**
     * @param  string|string[]  $index
     */
    public function scopeIgnoreIndex(Builder $query, $index): Builder
    {
        $table = $this->getTable();

        $index = $this->parseIndexName($index);

        $this->from[] = "IGNORE INDEX($index)";

        $raw = "`$table` ".implode(' ', $this->from);

        // @var Builder $query
        return $query->from(DB::raw($raw));
    }

    /**
     * @param  string|string[]  $index
     */
    protected function parseIndexName($index): string
    {
        if (\is_array($index)) {
            return '`'.implode('`, `', $index).'`';
        }

        return '`'.$index.'`';
    }
}
