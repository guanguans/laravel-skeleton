<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;
use InvalidArgumentException;

/**
 * @method static Builder|static pipe(...$pipes)
 */
trait QueryBuilderPipe
{
    /**
     * ```
     * User::query()
     *     ->pipe(
     *         function (Builder $builder, $next){
     *             $builder->where('id', '>', 10);
     *
     *             return $next($builder);
     *         },
     *         function (Builder $builder, $next){
     *             $builder->where('id', '<', 100);
     *
     *             return $next($builder);
     *         }
     *     )
     *     ->pipe(function (Builder $builder){
     *         return $builder->where('name', 'like', '张%');
     *     })
     *     // ->dd()
     *     ->get();
     * ```
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  array|string|\Closure  $pipes
     *
     * @return Builder
     */
    public function scopePipe(Builder $builder, ...$pipes)
    {
        array_unshift($pipes, function (Builder $builder, $next) {
            if (! $next($builder) instanceof Builder) {
                throw new InvalidArgumentException(sprintf('Query builder pipeline must be return a %s instance.', Builder::class));
            }
        });

        return (new Pipeline(app()))
            ->send($builder)
            ->through(...$pipes)
            ->thenReturn();
    }
}
