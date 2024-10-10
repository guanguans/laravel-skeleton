<?php

declare(strict_types=1);

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
