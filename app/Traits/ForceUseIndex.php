<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @method Builder useIndex(string|string[] $index)
 * @method Builder forceIndex(string|string[] $index)
 * @method Builder ignoreIndex(string|string[] $index)
 */
trait ForceUseIndex
{
    protected $from = [];

    /**
     * @param  string|array  $index
     *
     * @return string
     */
    protected function parseIndexName($index): string
    {
        if (is_array($index)) {
            return "`" . implode("`, `", $index) . "`";
        }

        return "`" . $index . "`";
    }

    /**
     * @param $query
     * @param  string|array  $index
     *
     * @return Builder
     */
    public function scopeUseIndex($query, $index): Builder
    {
        /* @var \Illuminate\Database\Eloquent\Model $this */
        $table = $this->getTable();

        $index = $this->parseIndexName($index);

        $this->from[] = "USE INDEX($index)";

        $raw = "`$table` " . implode(" ", $this->from);

        /* @var Builder $query */
        return $query->from(DB::raw($raw));
    }

    /**
     * @param $query
     * @param  string|array  $index
     *
     * @return Builder
     */
    public function scopeForceIndex($query, $index): Builder
    {
        /* @var \Illuminate\Database\Eloquent\Model $this */
        $table = $this->getTable();

        $index = $this->parseIndexName($index);

        $this->from[] = "FORCE INDEX($index)";

        $raw = "`$table` " . implode(" ", $this->from);

        /* @var Builder $query */
        return $query->from(DB::raw($raw));
    }

    /**
     * @param $query
     * @param  string|array  $index
     *
     * @return Builder
     */
    public function scopeIgnoreIndex($query, $index): Builder
    {
        /* @var \Illuminate\Database\Eloquent\Model $this */
        $table = $this->getTable();

        $index = $this->parseIndexName($index);

        $this->from[] = "IGNORE INDEX($index)";

        $raw = "`$table` " . implode(" ", $this->from);

        /* @var Builder $query */
        return $query->from(DB::raw($raw));
    }
}
