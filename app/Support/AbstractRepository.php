<?php

declare(strict_types=1);

namespace App\Support;

use App\Support\Traits\Cacheable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;

/**
 * @see https://github.com/Torann/laravel-repository
 */
abstract class AbstractRepository
{
    use Cacheable;

    /**
     * Cache expires constants
     */
    final public const EXPIRES_END_OF_DAY = 'eod';

    /**
     * Searching operator.
     *
     * This might be different when using a
     * different database driver.
     */
    public static string $searchOperator = 'LIKE';

    protected string $model;

    protected Model $modelInstance;

    /**
     * The errors message bag instance
     */
    protected MessageBag $errors;

    /** @var|\Illuminate\Database\Eloquent\Builder */
    protected $query;

    /**
     * Global query scope.
     */
    protected array $scopeQuery = [];

    /**
     * Valid orderable columns.
     *
     * @return array
     */
    protected $orderable = [];

    /**
     * Valid searchable columns
     *
     * @return array
     */
    protected $searchable = [];

    /**
     * Default order by column and direction pairs.
     */
    protected array $orderBy = [];

    /**
     * One time skip of ordering. This is done when the
     * ordering is overwritten by the orderBy method.
     */
    protected bool $skipOrderingOnce = false;

    /**
     * A set of keys used to perform range queries.
     */
    protected array $range_keys = [
        'lt', 'gt',
        'bt', 'ne',
    ];

    /**
     * Create a new Repository instance
     *
     * @throws \RuntimeException
     */
    public function __construct()
    {
        $this->makeModel();
        $this->boot();
    }

    /**
     * Handle dynamic static method calls into the method.
     */
    public function __call($method, $parameters): mixed
    {
        // Check for scope method and call
        if (method_exists($this, $scope = 'scope'.ucfirst($method))) {
            return \call_user_func_array([$this, $scope], $parameters);
        }

        $className = static::class;

        throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }

    /**
     * The "booting" method of the repository.
     */
    public function boot(): void {}

    /**
     * Return model instance.
     */
    public function getModel(): Model
    {
        return $this->modelInstance;
    }

    /**
     * Get a new entity instance
     */
    public function getNew(array $attributes = []): Model
    {
        $this->errors = new MessageBag();

        return $this->modelInstance->newInstance($attributes);
    }

    /**
     * Get a new query builder instance with the applied
     * the order by and scopes.
     *
     * @return $this
     */
    public function newQuery(bool $skipOrdering = false)
    {
        $this->query = $this->getNew()->newQuery();

        // Apply order by
        if (false === $skipOrdering && false === $this->skipOrderingOnce) {
            foreach ($this->getOrderBy() as $column => $dir) {
                $this->query->orderBy($column, $dir);
            }
        }

        // Reset the one time skip
        $this->skipOrderingOnce = false;

        $this->applyScope();

        return $this;
    }

    /**
     * Find data by its primary key.
     */
    public function find(mixed $id, array $columns = ['*']): Collection|Model
    {
        $this->newQuery();

        return $this->query->find($id, $columns);
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail(string $id, array $columns = ['*']): Model
    {
        $this->newQuery();

        if ($result = $this->query->find($id, $columns)) {
            return $result;
        }

        throw (new ModelNotFoundException())->setModel($this->model);
    }

    /**
     * Find data by field and value
     */
    public function findBy(string $field, string $value, array $columns = ['*']): Collection|Model
    {
        $this->newQuery();

        return $this->query->where($field, '=', $value)->first($columns);
    }

    /**
     * Find data by field
     */
    public function findAllBy(string $attribute, mixed $value, array $columns = ['*']): mixed
    {
        $this->newQuery();

        // Perform where in
        if (\is_array($value)) {
            return $this->query->whereIn($attribute, $value)->get($columns);
        }

        return $this->query->where($attribute, '=', $value)->get($columns);
    }

    /**
     * Find data by multiple fields
     */
    public function findWhere(array $where, array $columns = ['*']): mixed
    {
        $this->newQuery();

        foreach ($where as $field => $value) {
            if (\is_array($value)) {
                [$field, $condition, $val] = $value;
                $this->query->where($field, $condition, $val);
            } else {
                $this->query->where($field, '=', $value);
            }
        }

        return $this->query->get($columns);
    }

    /**
     * Order results by.
     */
    public function orderBy(string $column, string $direction): self
    {
        // Ensure the sort is valid
        if (false === \in_array($column, $this->getOrderable(), true)
            && false === \array_key_exists($column, $this->getOrderable())
        ) {
            return $this;
        }

        // One time skip
        $this->skipOrderingOnce = true;

        return $this->addScopeQuery(function ($query) use ($column, $direction) {
            // Get valid sort order
            $direction = \in_array(strtolower($direction), ['desc', 'asc'], true) ? $direction : 'asc';

            // Check for table column mask
            $column = Arr::get($this->getOrderable(), $column, $column);

            return $query->orderBy($this->appendTableName($column), $direction);
        });
    }

    /**
     * Return the order by array.
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * Set searchable array.
     */
    public function setSearchable(array|string $key, mixed $value = null): self
    {
        // Allow for a batch assignment
        if (false === \is_array($key)) {
            $key = [$key => $value];
        }

        // Update the searchable values
        foreach ($key as $k => $v) {
            $this->searchable[$k] = $v;
        }

        return $this;
    }

    /**
     * Return searchable keys.
     */
    public function getSearchableKeys(): array
    {
        $return = $this->getSearchable();

        return array_values(array_map(static fn ($value, $key) => (\is_array($value) || false === is_numeric($key)) ? $key : $value, $return, array_keys($return)));
    }

    /**
     * Return searchable array.
     */
    public function getSearchable(): array
    {
        return $this->searchable;
    }

    /**
     * Return orderable array.
     */
    public function getOrderable(): array
    {
        return $this->orderable;
    }

    /**
     * Filter results by given query params.
     */
    public function search(array|string $queries): self
    {
        // Adjust for simple search queries
        if (\is_string($queries)) {
            $queries = [
                'query' => $queries,
            ];
        }

        return $this->addScopeQuery(function ($query) use ($queries) {
            // Keep track of what tables have been joined and their aliases
            $joined = [];

            foreach ($this->getSearchable() as $param => $columns) {
                // It doesn't always have to map to something
                $param = is_numeric($param) ? $columns : $param;

                // Get param value
                $value = Arr::get($queries, $param, '');

                // Validate value
                if ('' === $value || null === $value) {
                    continue;
                }

                // Columns should be an array
                $columns = (array) $columns;

                // Loop though the columns and look for relationships
                foreach ($columns as $key => $column) {
                    @[$joiningTable, $options] = explode(':', $column);
                    @[$column, $foreignKey, $relatedKey, $alias] = explode(',', $options);
                    // Join the table if it hasn't already been joined
                    if (false === isset($joined[$joiningTable])) {
                        $joined[$joiningTable] = $this->addSearchJoin(
                            $query,
                            $joiningTable,
                            $foreignKey,
                            $relatedKey ?: $param, // Allow for related key overriding
                            $alias
                        );
                    }

                    $columns[$key] = "{$joined[$joiningTable]}.{$column}";
                }

                // Perform a range based query if the range is valid
                // and the separator matches.
                if ($this->createSearchRangeClause($query, $value, $columns)) {
                    continue;
                }

                // Create standard query
                if (\count($columns) > 1) {
                    $query->where(function ($q) use ($columns, $param, $value): void {
                        foreach ($columns as $column) {
                            $this->createSearchClause($q, $param, $column, $value, 'or');
                        }
                    });
                } else {
                    $this->createSearchClause($query, $param, $columns[0], $value);
                }
            }

            // Ensure only the current model's table attributes are return
            $query->addSelect([
                $this->getModel()->getTable().'.*',
            ]);

            return $query;
        });
    }

    /**
     * Set the "limit" value of the query.
     */
    public function limit(int $limit): self
    {
        return $this->addScopeQuery(static fn ($query) => $query->limit($limit));
    }

    /**
     * Retrieve all data of repository
     */
    public function all(array $columns = ['*']): Collection
    {
        $this->newQuery();

        return $this->query->get($columns);
    }

    /**
     * Retrieve the "count" result of the query.
     */
    public function count(array $columns = ['*']): int
    {
        $this->newQuery();

        return $this->query->count($columns);
    }

    /**
     * Get an array with the values of a given column.
     */
    public function pluck(string $value, ?string $key = null): array
    {
        $this->newQuery();

        $lists = $this->query->pluck($value, $key);

        if (\is_array($lists)) {
            return $lists;
        }

        return $lists->all();
    }

    /**
     * Retrieve all data of repository, paginated
     */
    public function paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        // Get the default per page when not set
        $perPage = $perPage ?: config('repositories.per_page', 15);

        // Get the per page max
        $perPageMax = config('repositories.max_per_page', 100);

        // Ensure the user can never make the per
        // page limit higher than the defined max.
        if ($perPage > $perPageMax) {
            $perPage = $perPageMax;
        }

        $this->newQuery();

        return $this->query->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * Retrieve all data of repository, paginated
     */
    public function simplePaginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\Paginator
    {
        $this->newQuery();

        // Get the default per page when not set
        $perPage = $perPage ?: config('repositories.per_page', 15);

        return $this->query->simplePaginate($perPage, $columns, $pageName, $page);
    }

    /**
     * Save a new entity in repository
     */
    public function create(array $attributes): bool|Model
    {
        $entity = $this->getNew($attributes);

        if ($entity->save()) {
            $this->flushCache();

            return $entity;
        }

        return false;
    }

    /**
     * Update an entity with the given attributes and persist it
     */
    public function update(Model $entity, array $attributes): bool
    {
        if ($entity->update($attributes)) {
            $this->flushCache();

            return true;
        }

        return false;
    }

    /**
     * Delete a entity in repository
     *
     * @throws \Exception
     */
    public function delete(mixed $entity): ?bool
    {
        if (($entity instanceof Model) === false) {
            $entity = $this->find($entity);
        }

        if ($entity->delete()) {
            $this->flushCache();

            return true;
        }

        return false;
    }

    /**
     * Create model instance.
     *
     * @throws \RuntimeException
     */
    public function makeModel(): Builder
    {
        if (empty($this->model)) {
            throw new \RuntimeException('The model class must be set on the repository.');
        }

        return $this->modelInstance = new $this->model();
    }

    /**
     * Get a new query builder instance with the applied
     * the order by and scopes.
     */
    public function getBuilder(bool $skipOrdering = false): Builder
    {
        $this->newQuery($skipOrdering);

        return $this->query;
    }

    /**
     * Get the raw SQL statements for the request
     */
    public function toSql(): string
    {
        $this->newQuery();

        return $this->query->toSql();
    }

    /**
     * Return query scope.
     */
    public function getScopeQuery(): array
    {
        return $this->scopeQuery;
    }

    /**
     * Add query scope.
     *
     * @return $this
     */
    public function addScopeQuery(\Closure $scope)
    {
        $this->scopeQuery[] = $scope;

        return $this;
    }

    /**
     * Add a message to the repository's error messages.
     */
    public function addError(string $message, string $key = 'message'): self
    {
        $this->getErrors()->add($key, $message);

        return $this;
    }

    /**
     * Get the repository's error messages.
     */
    public function getErrors(): MessageBag
    {
        if (! $this->errors instanceof MessageBag) {
            $this->errors = new MessageBag();
        }

        return $this->errors;
    }

    /**
     * Get the repository's first error message.
     */
    public function getErrorMessage(string $default = ''): string
    {
        return $this->getErrors()->first('message') ?: $default;
    }

    /**
     * Reset internal Query
     *
     * @return $this
     */
    protected function scopeReset()
    {
        $this->scopeQuery = [];

        $this->query = $this->newQuery();

        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope()
    {
        foreach ($this->scopeQuery as $callback) {
            if (\is_callable($callback)) {
                $this->query = $callback($this->query);
            }
        }

        // Clear scopes
        $this->scopeQuery = [];

        return $this;
    }

    /**
     * Append table name to column.
     */
    protected function appendTableName(string $column): string
    {
        // If missing prepend the table name
        if (! str_contains($column, '.')) {
            return $this->modelInstance->getTable().'.'.$column;
        }

        // Remove alias prefix indicator
        if (false !== preg_match('/^_\./', $column)) {
            return preg_replace('/^_\./', '', $column);
        }

        return $column;
    }

    /**
     * Add a search where clause to the query.
     */
    protected function createSearchClause(Builder $query, string $param, string $column, mixed $value, string $boolean = 'and'): void
    {
        if ('query' === $param) {
            $query->where($this->appendTableName($column), self::$searchOperator, '%'.$value.'%', $boolean);
        } elseif (\is_array($value)) {
            $query->whereIn($this->appendTableName($column), $value, $boolean);
        } else {
            $query->where($this->appendTableName($column), '=', $value, $boolean);
        }
    }

    /**
     * Add a search join to the query.
     */
    protected function addSearchJoin(Builder $query, string $joiningTable, string $foreignKey, string $relatedKey, string $alias): string
    {
        // We need to join to the intermediate table
        $localTable = $this->getModel()->getTable();

        // Set the way the table will be join, with an alias or without
        $table = $alias ? "{$joiningTable} as {$alias}" : $joiningTable;

        // Create an alias for the join
        $alias = $alias ?: $joiningTable;

        // Create the join
        $query->join($table, "{$alias}.{$foreignKey}", "{$localTable}.{$relatedKey}");

        return $alias;
    }

    /**
     * Add a range clause to the query.
     */
    protected function createSearchRangeClause(Builder $query, mixed $value, array $columns): bool
    {
        // Skip arrays
        if (\is_array($value)) {
            return false;
        }

        // Get the range type
        $rangeType = strtolower(substr($value, 0, 2));

        // Perform a range based query if the range is valid
        // and the separator matches.
        if (':' === substr($value, 2, 1) && \in_array($rangeType, $this->range_keys, true)) {
            // Get the true value
            $value = substr($value, 3);

            switch ($rangeType) {
                case 'gt':
                    $query->where($this->appendTableName($columns[0]), '>', $value, 'and');

                    break;

                case 'lt':
                    $query->where($this->appendTableName($columns[0]), '<', $value, 'and');

                    break;

                case 'ne':
                    $query->where($this->appendTableName($columns[0]), '<>', $value, 'and');

                    break;

                case 'bt':
                    // Because this can only have two values
                    if (2 === \count($values = explode(',', $value))) {
                        $query->whereBetween($this->appendTableName($columns[0]), $values);
                    }

                    break;
            }

            return true;
        }

        return false;
    }
}
