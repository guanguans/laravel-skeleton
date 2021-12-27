<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

/**
 * @method Builder|static pipe($middleware)
 */
trait QueryBuilderPipe
{
    /**
     * ```
     * User::query()
     *     ->pipe(function (Builder $builder, Closure $next){
     *         $builder->where('id', '>', 1);
     *
     *         return $next($builder);
     *     })
     *     ->pipe(function (Builder $builder){
     *         $builder->limit(10);
     *     })
     *     ->get();
     * ```
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  array|string|\Closure  $pipes
     *
     * @return Builder
     */
    public function scopePipe(Builder $builder, $pipes)
    {
        return app(Pipeline::class)
            ->send($builder)
            ->through($pipes)
            ->thenReturn();
    }
}
