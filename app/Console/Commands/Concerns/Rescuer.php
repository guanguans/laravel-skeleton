<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Console\Commands\Concerns;

use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * @mixin \Illuminate\Console\Command
 */
trait Rescuer
{
    /**
     * @template TValue
     *
     * @noinspection RedundantDocCommentTagInspection
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     *
     * @param  callable(): TValue  $callback
     * @param  bool|callable(\Throwable): bool  $report
     * @return \Throwable|TValue
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function rescue(callable $callback, bool|callable $report = false): mixed
    {
        return rescue(
            $callback,
            function (\Throwable $throwable): \Throwable {
                $this->laravel->make(ExceptionHandler::class)->renderForConsole($this->output, $throwable);

                return $throwable;
            },
            $report
        );
    }
}
