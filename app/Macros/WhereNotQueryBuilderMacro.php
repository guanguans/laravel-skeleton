<?php

namespace App\Macros;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 * @see https://github.com/protonemedia/laravel-eloquent-where-not
 */
class WhereNotQueryBuilderMacro
{
    /**
     * The count for each table.
     *
     * @var array
     */
    protected static $tableSubCount = [];

    public static function whereNot(): callable
    {
        return function ($withQuery): Builder {
            $callable = is_callable($withQuery)
                ? $withQuery
                : transform($withQuery, function ($value): callable {
                    // We both allow single and multiple scopes...
                    $scopes = Arr::wrap($value);

                    return function ($query) use ($scopes) {
                        // If $scope is numeric, there are no arguments, and we can
                        // safely assume the scope is in the $arguments variable.
                        foreach ($scopes as $scope => $arguments) {
                            if (is_numeric($scope)) {
                                [$scope, $arguments] = [$arguments, null];
                            }

                            // As we allow a constraint to be a single arguments.
                            $arguments = Arr::wrap($arguments);

                            $query->{$scope}(...$arguments);
                        }

                        return $query;
                    };
                });

            /** @var \Illuminate\Database\Eloquent\Builder $builder */
            $builder = $this;

            return $builder->whereNotExists(function ($query) use ($callable, $builder) {
                // Create a new Eloquent Query Builder with the given Query Builder and
                // set the model from the original builder.
                $query = new Builder($query);
                $query->setModel($model = $builder->getModel());

                $qualifiedKeyName = $model->getQualifiedKeyName();
                $originalTable = $model->getTable();

                // Instantiate a new model that uses the aliased table.
                $aliasedTable = transform($originalTable, function ($table) {
                    if (! array_key_exists($table, static::$tableSubCount)) {
                        static::$tableSubCount[$table] = 0;
                    }

                    $count = static::$tableSubCount[$table]++;

                    return "where_not_{$count}_{$table}";
                });
                $aliasedModel = $query->newModelInstance()->setTable($aliasedTable);

                // Apply the where constraint based on the model's key name and apply the $callable.
                $query
                    ->setModel($aliasedModel)
                    ->select(DB::raw(1))
                    ->from($originalTable, $aliasedTable)
                    ->whereColumn($aliasedModel->getQualifiedKeyName(), $qualifiedKeyName)
                    ->limit(1)
                    ->tap(function ($query) use ($callable) {
                        return $callable($query);
                    });
            });
        };
    }
}
