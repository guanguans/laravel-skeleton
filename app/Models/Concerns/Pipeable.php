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
     *             $builder->where('id', '>', 10);.
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
     * @param list<callable> $pipes
     *
     * @throws \Throwable
     */
    protected function scopePipe(Builder $builder, ...$pipes): Builder
    {
        array_unshift($pipes, static function (Builder $builder, $next): void {
            throw_unless(
                $next($builder) instanceof Builder,
                \InvalidArgumentException::class,
                \sprintf('Query builder pipeline must be return a %s instance.', Builder::class)
            );
        });

        return (new Pipeline(app()))
            ->send($builder)
            ->through(...$pipes)
            ->thenReturn();
    }
}
