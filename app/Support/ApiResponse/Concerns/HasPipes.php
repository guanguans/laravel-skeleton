<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\ApiResponse\Concerns;

use Illuminate\Support\Collection;

/**
 * @mixin \App\Support\ApiResponse\ApiResponse
 */
trait HasPipes
{
    private Collection $pipes;

    public function unshiftPipes(...$pipes): self
    {
        return $this->tapPipes(static function (Collection $originalPipes) use ($pipes): void {
            $originalPipes->unshift(...$pipes);
        });
    }

    public function pushPipes(...$pipes): self
    {
        return $this->tapPipes(static function (Collection $originalPipes) use ($pipes): void {
            $originalPipes->push(...$pipes);
        });
    }

    public function extendPipes(callable $callback): self
    {
        $this->pipes = $callback($this->pipes);

        return $this;
    }

    public function tapPipes(callable $callback): self
    {
        tap($this->pipes, $callback);

        return $this;
    }

    private function pipes(): array
    {
        return $this->pipes->all();
    }
}
