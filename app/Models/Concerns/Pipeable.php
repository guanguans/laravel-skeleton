<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

/**
 * @method static Builder|static pipe(...$pipes)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Pipeable
{
    /**
     * ```
     * User::query()
     *     ->pipe(
     *         function (Builder $builder, $next): Builder {
     *             $builder->where('id', '>', 10);
     *
     *             return $next($builder);
     *         },
     *         function (Builder $builder, $next): Builder {
     *             $builder->where('id', '<', 100);
     *
     *             return $next($builder);
     *         }
     *     )
     *     ->pipe(function (Builder $builder): Builder {
     *         return $builder->where('name', 'like', '张%');
     *     })
     *     // ->dd()
     *     ->get();
     * ```
     *
     * @param  callable[]  $pipes
     */
    public function scopePipe(Builder $builder, ...$pipes): Builder
    {
        array_unshift($pipes, function (Builder $builder, $next) {
            if (! $next($builder) instanceof Builder) {
                throw new \InvalidArgumentException(
                    sprintf('Query builder pipeline must be return a %s instance.', Builder::class)
                );
            }
        });

        return (new Pipeline(app()))
            ->send($builder)
            ->through(...$pipes)
            ->thenReturn();
    }
}
