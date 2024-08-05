<?php

declare(strict_types=1);

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

    public function pipes(): array
    {
        return $this->pipes->all();
    }
}
