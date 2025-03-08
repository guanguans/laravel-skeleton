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

namespace App\Support\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
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
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @see https://github.com/thiagoprz/crud-tools
 */
trait ModelCrudable
{
    /**
     * @see ModelCrudable::$validations
     */
    public static function validations(?int $id = null): array
    {
        return array_map(static function (array $rules) use ($id): array {
            foreach ($rules as $scenario => $rule) {
                $rules[$scenario] = Str::replace('$id', $id, $rule);
            }

            return $rules;
        }, self::$validations);
    }

    /**
     * Return the validations for the given scenario.
     *
     * @see ModelCrudable::$validations
     */
    public static function validateOn(string $scenario = 'create', ?int $id = null): array
    {
        $validations = self::validations($id);

        if ('update' === $scenario && empty($validations['update'])) {
            $scenario = 'create';
        }

        return self::$validations[$scenario];
    }

    public static function search(array $data): LengthAwarePaginator
    {
        // Starts query
        $query = self::query();

        $searchableFields = method_exists(self::class, 'searchable') ? self::searchable() : self::$searchable;
        $query->where(static function (\Illuminate\Contracts\Database\Query\Builder $where) use ($data, $searchableFields): void {
            foreach ($searchableFields as $field => $type) {
                if (str_contains($field, '.')) {
                    continue;
                }

                self::buildQuery($where, $field, $type, $data);
            }
        });

        foreach ($searchableFields as $field => $definition) {
            if (!str_contains($field, '.')) {
                continue;
            }

            $arr = explode('.', $field);
            $realField = $arr[1];
            $table = $arr[0];
            $query->whereHas($table, static function (\Illuminate\Contracts\Database\Query\Builder $where) use ($data, $realField, $definition): void {
                self::buildQuery($where, $realField, $definition['type'], $data, $definition['table'].'.'.$realField);
            });
        }

        // Gets related records attached to
        self::searchWith($query);

        // Gets count result for related records attached to
        self::searchWithCount($query);

        // Defines "order by"
        self::searchOrder($query, $data);

        /*
         * If model uses SoftDeletes allows query excluded records
         *
         * @see ModelCrudable::$onlyTrashedForbidden
         * @see ModelCrudable::$withTrashedForbidden
         */
        if (\in_array(SoftDeletes::class, class_uses(self::class), true)) {
            self::applyOnlyTrashed($query, $data);
            self::applyWithTrashed($query, $data);
        }

        $result = !empty($data['no_pagination']) && !isset(self::$noPaginationForbidden) ? $query->get() : self::setSearchPagination($query);

        if (!empty(self::$resourceForSearch)) {
            return self::$resourceForSearch::collection($result);
        }

        return $result;
    }

    /**
     * @see ModelCrudable::$searchOrder
     */
    public static function searchOrder(Builder $query, array $data): void
    {
        if (isset($data['order'])) {
            $sortFields = array_map(static fn ($item): array => explode(',', $item), explode('|', $data['order']));

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
     * Attaches related records to every result on the search query.
     *
     * @see ModelCrudable::$searchCount
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
     * @see ModelCrudable::$searchWith
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
     * @see ModelCrudable::$paginationForSearch
     */
    public static function setSearchPagination(Builder $query): LengthAwarePaginator
    {
        $pagination = self::$paginationForSearch ?? 10;

        return $query->paginate($pagination);
    }

    /**
     * @see ModelCrudable::$withTrashedForbidden
     */
    public static function applyWithTrashed(Builder $query, array $data): void
    {
        if (!self::$withTrashedForbidden && $data['with_trashed']) {
            $query->withTrashed();
        }
    }

    /**
     * @see ModelCrudable::$onlyTrashedForbidden
     */
    public static function applyOnlyTrashed(Builder $query, array $data): void
    {
        if (!self::$onlyTrashedForbidden && $data['only_trashed']) {
            $query->onlyTrashed();
        }
    }

    public static function fileUploads(Model $model): array
    {
        return [];
    }

    /**
     * Builds the main query based on a informed field.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Query builder command
     * @param string $field "The" field
     * @param string $type Type of field (string, int, date, datetime...)
     * @param array $data Data sent on $request
     * @param ?string $aliasField Alias name for field (where inside a related table "table.column")
     */
    private static function buildQuery(Builder $query, string $field, string $type, array $data, ?string $aliasField = null): void
    {
        if (!$aliasField) {
            $aliasField = $field;
        }

        if (isset($data[$field]) && null !== $data[$field]) {
            $customMethod = 'search'.ucfirst($field);

            if (method_exists(self::class, $customMethod)) {
                // If field has custom "search" method uses it
                $query->where(static function (\Illuminate\Contracts\Database\Query\Builder $query) use ($field, $data, $customMethod): void {
                    self::$customMethod($query, $data[$field]);
                });
            } elseif ('string_match' === $type || 'date' === $type || 'datetime' === $type || 'int' === $type) {
                // Exact search
                self::exactFilter($query, $field, $data, $aliasField);
            } elseif ('string' === $type) { // Like Search
                self::likeFilter($query, $field, $data, $aliasField);
            }
        }

        // Date, Datetime and Decimal implementation for range field search (_from and _to suffixed fields)
        if ('date' === $type || 'datetime' === $type || 'decimal' === $type || 'int' === $type) {
            self::rangeFilter($query, $field, $data, $aliasField, $type);
        }
    }

    private static function exactFilter(Builder $query, string $field, array $data, string $aliasField): void
    {
        if (\is_array($data[$field])) {
            $query->where(static function (\Illuminate\Contracts\Database\Query\Builder $query) use ($field, $data, $aliasField): void {
                foreach ($data[$field] as $datum) {
                    $query->orWhere($aliasField, $datum);
                }
            });
        } elseif (str_starts_with($data[$field], '!=')) {
            $query->where($field, '!=', str_replace('!=', '', $data[$field]));
        } else {
            $query->where($field, $data[$field]);
        }
    }

    private static function likeFilter(Builder $query, string $field, array $data, string $aliasField): void
    {
        if (\is_array($data[$field])) {
            $query->where(static function (\Illuminate\Contracts\Database\Query\Builder $query) use ($field, $data, $aliasField): void {
                foreach ($data[$field] as $datum) {
                    $query->orWhere($aliasField, 'LIKE', '%'.$datum.'%');
                }
            });
        } else {
            $query->where($field, 'LIKE', '%'.$data[$field].'%');
        }
    }

    private static function rangeFilter(Builder $query, string $field, array $data, string $aliasField, string $type): void
    {
        if (!empty($data[$field.'_from'])) {
            $value = $data[$field.'_from'];

            if ('datetime' === $type && \strlen($value) < 16) { // If datetime was informed only by its date (Y-m-d instead of Y-m-d H:i:s)
                $value .= ' 00:00:00';
            }

            $query->where($field, '>=', $value);
        }

        if (!empty($data[$field.'_to'])) {
            $value = $data[$field.'_to'];

            if ('datetime' === $type && \strlen($value) < 16) { // If datetime was informed only by its date (Y-m-d instead of Y-m-d H:i:s)
                $value .= ' 23:59:59';
            }

            $query->where($aliasField, '<=', $value);
        }
    }
}
