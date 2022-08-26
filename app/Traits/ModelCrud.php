<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Trait ModelCrud.
 *
 * @property array $validations Validations definitions on create, update and delete scenarios
 * @property array $searchable Allows specifying fields that can be searched on search() method
 * @property array $searchOrder Defines search() method order fields. Through request use field with name order and defined value like this: "field,direction|field_2,direction_2|..." (use as many fields to order as you wish just separating them with pipes "|")
 * @property array $searchWith Defines the relations to be brought in the search() method
 * @property array $searchCount Defines which relationship will be counted along in the search() method. Use standard Laravel (see https://laravel.com/docs/master/eloquent-relationships#counting-related-models)
 * @property array $resourceForSearch Defines a Resource to be used as the return of the search() method allowing to use Resources on api's for instance (see https://laravel.com/docs/master/eloquent-resources)
 * @property int $paginationForSearch Pagination Variable
 * @property bool $withTrashedForbidden withTrashed() gets forbidden on this class
 * @property bool $onlyTrashedForbidden onlyTrashed() gets forbidden on this class
 * @property bool $noPaginationForbidden allow remove pagination forbidden on this class
 *
 * @see https://github.com/thiagoprz/crud-tools
 */
trait ModelCrud
{
    /**
     * @see ModelCrud::$validations
     *
     * @param  null|int  $id
     *
     * @return array
     */
    public static function validations(?int $id = null): array
    {
        return array_map(function (array $rules) use ($id) {
            foreach ($rules as $scenario => $rule) {
                $rules[$scenario] = Str::replace('$id', $id, $rule);
            }

            return $rules;
        }, self::$validations);
    }

    /**
     * Return the validations for the given scenario
     *
     * @see ModelCrud::$validations
     *
     * @param  string  $scenario
     * @param  null|int  $id
     *
     * @return array
     */
    public static function validateOn(string $scenario = 'create', ?int $id = null): array
    {
        $validations = self::validations($id);
        if ($scenario === 'update' && empty($validations['update'])) {
            $scenario = 'create';
        }

        return self::$validations[$scenario];
    }

    /**
     * @param  array  $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function search(array $data)
    {
        // Starts query
        $query = self::query();

        $searchableFields = method_exists(__CLASS__, 'searchable') ? self::searchable() : self::$searchable;
        $query->where(function ($where) use ($data, $searchableFields) {
            foreach ($searchableFields as $field => $type) {
                if (strpos($field, '.') !== false) {
                    continue;
                }
                self::buildQuery($where, $field, $type, $data);
            }
        });

        foreach ($searchableFields as $field => $definition) {
            if (strpos($field, '.') === false) {
                continue;
            }
            $arr = explode('.', $field);
            $realField = $arr[1];
            $table = $arr[0];
            $query->whereHas($table, function ($where) use ($data, $realField, $definition) {
                self::buildQuery($where, $realField, $definition['type'], $data, $definition['table'] . '.' . $realField);
            });
        }

        // Gets related records attached to
        self::searchWith($query);

        // Gets count result for related records attached to
        self::searchWithCount($query);

        // Defines "order by"
        self::searchOrder($query, $data);

        /**
         * If model uses SoftDeletes allows query excluded records
         * @see ModelCrud::$onlyTrashedForbidden
         * @see ModelCrud::$withTrashedForbidden
         */
        if (in_array(SoftDeletes::class, class_uses(self::class), true)) {
            self::applyOnlyTrashed($query, $data);
            self::applyWithTrashed($query, $data);
        }

        $result = ! empty($data['no_pagination']) && ! isset(self::$noPaginationForbidden) ? $query->get() : self::setSearchPagination($query);
        if (! empty(self::$resourceForSearch)) {
            return self::$resourceForSearch::collection($result);
        }

        return $result;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $data
     *
     * @return void
     *@see ModelCrud::$searchOrder
     *
     */
    public static function searchOrder(Builder $query, array $data): void
    {
        if (isset($data['order'])) {
            $sortFields = array_map(function ($item) {
                return explode(',', $item);
            }, explode('|', $data['order']));
            foreach ($sortFields as $sortField) {
                $query->orderBy($sortField[0], $sortField[1] ?? 'ASC');
            }
        } elseif (isset(self::$searchOrder)) {
            foreach (self::$searchOrder as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        }
    }

    /**
     * Attaches related records to every result on the search query
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return void
     *@see ModelCrud::$searchCount
     *
     */
    public static function searchWithCount(Builder $query): void
    {
        if (isset(self::$searchCount)) {
            foreach (self::$searchCount as $searchCountable) {
                $query->withCount($searchCountable);
            }
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return void
     *@see ModelCrud::$searchWith
     */
    public static function searchWith(Builder $query): void
    {
        if (isset(self::$searchWith)) {
            foreach (self::$searchWith as $searchWithable) {
                $query->with($searchWithable);
            }
        }
    }

    /**
     * @see ModelCrud::$paginationForSearch
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function setSearchPagination(Builder $query): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $pagination = self::$paginationForSearch ?? 10;

        return $query->paginate($pagination);
    }

    /**
     * @see ModelCrud::$withTrashedForbidden
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $data
     *
     * @return void
     */
    public static function applyWithTrashed(Builder $query, array $data): void
    {
        if (! self::$withTrashedForbidden && $data['with_trashed']) {
            $query->withTrashed();
        }
    }

    /**
     * @see ModelCrud::$onlyTrashedForbidden
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $data
     *
     * @return void
     */
    public static function applyOnlyTrashed(Builder $query, array $data): void
    {
        if (! self::$onlyTrashedForbidden && $data['only_trashed']) {
            $query->onlyTrashed();
        }
    }

    /**
     * Builds the main query based on a informed field
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query Query builder command
     * @param  string  $field "The" field
     * @param  string  $type Type of field (string, int, date, datetime...)
     * @param  array  $data Data sent on $request
     * @param  string|null  $aliasField Alias name for field (where inside a related table "table.column")
     *
     * @return void
     */
    private static function buildQuery(Builder $query, string $field, string $type, array $data, string $aliasField = null): void
    {
        if (! $aliasField) {
            $aliasField = $field;
        }
        if (isset($data[$field]) && $data[$field] !== null) {
            $customMethod = 'search' . ucfirst($field);
            if (method_exists(self::class, $customMethod)) { // If field has custom "search" method uses it
                $query->where(function ($query) use ($field, $data, $customMethod) {
                    self::$customMethod($query, $data[$field]);
                });
            } else {
                if ($type === 'string_match' || $type === 'date' || $type === 'datetime' || $type === 'int') { // Exact search
                    self::exactFilter($query, $field, $data, $aliasField);
                } elseif ($type === 'string') { // Like Search
                    self::likeFilter($query, $field, $data, $aliasField);
                }
            }
        }
        // Date, Datetime and Decimal implementation for range field search (_from and _to suffixed fields)
        if ($type === 'date' || $type === 'datetime' || $type === 'decimal' || $type === 'int') {
            self::rangeFilter($query, $field, $data, $aliasField, $type);
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $field
     * @param  array  $data
     * @param  string  $aliasField
     *
     * @return void
     */
    private static function exactFilter(Builder $query, string $field, array $data, string $aliasField): void
    {
        if (is_array($data[$field])) {
            $query->where(function ($query) use ($field, $data, $aliasField) {
                foreach ($data[$field] as $datum) {
                    $query->orWhere($aliasField, $datum);
                }
            });
        } elseif (strncmp($data[$field], '!=', 2) === 0) {
            $query->where($field, '!=', str_replace('!=', '', $data[$field]));
        } else {
            $query->where($field, $data[$field]);
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $field
     * @param  array  $data
     * @param  string  $aliasField
     *
     * @return void
     */
    private static function likeFilter(Builder $query, string $field, array $data, string $aliasField): void
    {
        if (is_array($data[$field])) {
            $query->where(function ($query) use ($field, $data, $aliasField) {
                foreach ($data[$field] as $datum) {
                    $query->orWhere($aliasField, 'LIKE', '%' . $datum . '%');
                }
            });
        } else {
            $query->where($field, 'LIKE', '%' . $data[$field] . '%');
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $field
     * @param  array  $data
     * @param  string  $aliasField
     * @param  string  $type
     *
     * @return void
     */
    private static function rangeFilter(Builder $query, string $field, array $data, string $aliasField, string $type): void
    {
        if (! empty($data[$field . '_from'])) {
            $value = $data[$field . '_from'];
            if ($type === 'datetime' && strlen($value) < 16) { // If datetime was informed only by its date (Y-m-d instead of Y-m-d H:i:s)
                $value .= ' 00:00:00';
            }
            $query->where($field, '>=', $value);
        }
        if (! empty($data[$field . '_to'])) {
            $value = $data[$field . '_to'];
            if ($type === 'datetime' && strlen($value) < 16) { // If datetime was informed only by its date (Y-m-d instead of Y-m-d H:i:s)
                $value .= ' 23:59:59';
            }
            $query->where($aliasField, '<=', $value);
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     *
     * @return array
     */
    public static function fileUploads(Model $model): array
    {
        return [];
    }
}
