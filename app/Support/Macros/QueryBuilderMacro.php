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
}
