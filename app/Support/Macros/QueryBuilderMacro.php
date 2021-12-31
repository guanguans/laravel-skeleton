<?php

namespace App\Support\Macros;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;
use InvalidArgumentException;

class QueryBuilderMacro
{
    public function pipe(): callable
    {
        return function (...$pipes): Builder {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return tap($this, function (Builder $builder) use ($pipes) {
                array_unshift($pipes, function (Builder $builder, $next) {
                    if (! $next($builder) instanceof Builder) {
                        throw new InvalidArgumentException(sprintf('Query builder pipeline must be return a %s instance.', Builder::class));
                    }
                });

                (new Pipeline(app()))
                    ->send($builder)
                    ->through(...$pipes)
                    ->thenReturn();
            });
        };
    }

    public function getToArray(): callable
    {
        return function ($columns = ['*']): array {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            return $this->get($columns)->toArray();
        };
    }

    public function firstToArray(): callable
    {
        return function ($columns = ['*']): ?array {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            // return optional($this->first($columns))->toArray();
            return ($model = $this->first($columns)) ? $model->toArray() : (array)$model;
        };
    }
}
