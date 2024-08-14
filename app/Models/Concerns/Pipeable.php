<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

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
     * @param  array<callable>  $pipes
     */
    public function scopePipe(Builder $builder, ...$pipes): Builder
    {
        array_unshift($pipes, static function (Builder $builder, $next): void {
            throw_unless($next($builder) instanceof Builder, \InvalidArgumentException::class, \sprintf('Query builder pipeline must be return a %s instance.', Builder::class));
        });

        return (new Pipeline(app()))
            ->send($builder)
            ->through(...$pipes)
            ->thenReturn();
    }
}
