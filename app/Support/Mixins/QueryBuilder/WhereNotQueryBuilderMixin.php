<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins\QueryBuilder;

use App\Support\Attributes\Mixin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @mixin \Illuminate\Database\Eloquent\Relations\Relation
 *
 * @see https://github.com/protonemedia/laravel-eloquent-where-not
 */
#[Mixin(Builder::class)]
#[Mixin(\Illuminate\Database\Query\Builder::class)]
#[Mixin(\Illuminate\Database\Eloquent\Relations\Relation::class)]
class WhereNotQueryBuilderMixin
{
    /**
     * The count for each table.
     */
    protected static array $tableSubCount = [];

    public static function whereNot(): callable
    {
        return function ($withQuery) {
            $callable = \is_callable($withQuery)
                ? $withQuery
                : transform($withQuery, static function ($value): callable {
                    // We both allow single and multiple scopes...
                    $scopes = Arr::wrap($value);

                    return static function ($query) use ($scopes) {
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

            return $builder->whereNotExists(static function (\Illuminate\Contracts\Database\Query\Builder $query) use ($callable, $builder): void {
                // Create a new Eloquent Query Builder with the given Query Builder and
                // set the model from the original builder.
                $query = new Builder($query);
                $query->setModel($model = $builder->getModel());

                $qualifiedKeyName = $model->getQualifiedKeyName();
                $originalTable = $model->getTable();

                // Instantiate a new model that uses the aliased table.
                $aliasedTable = transform($originalTable, static function ($table): string {
                    if (! \array_key_exists($table, static::$tableSubCount)) {
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
                    ->tap(static fn (\Illuminate\Contracts\Database\Query\Builder $query) => $callable($query));
            });
        };
    }
}
