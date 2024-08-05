<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Concerns;

/**
 * @mixin \App\Support\ApiResponse\ApiResponse
 */
trait HasExceptionMap
{
    /**
     * @var array<class-string<\Throwable>, callable|array{
     *     message: string|callable(\Throwable): string,
     *     code: int|callable(\Throwable): int,
     * }>
     */
    private array $exceptionMap = [];

    public function getExceptionMap(): array
    {
        return $this->exceptionMap;
    }

    public function setExceptionMap(array $exceptionMap): self
    {
        $this->exceptionMap = $exceptionMap;

        return $this;
    }

    /**
     * @param  class-string<\Throwable>  $exception
     * @param  callable|array{
     *     message: string|callable(\Throwable): string,
     *     code: int|callable(\Throwable): int,
     * }  $map
     */
    public function withExceptionMap(string $exception, callable|array $map): self
    {
        $this->exceptionMap[$exception] = $map;

        return $this;
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     *
     * @return null|array{
     *     message: string,
     *     code: int,
     * }
     */
    public function resolveExceptionMap(\Throwable $throwable): ?array
    {
        if (isset($this->exceptionMap[$throwable::class])) {
            $map = $this->exceptionMap[$throwable::class];

            return \is_callable($map) ? $map($throwable) : $map;
        }

        return null;
    }
}
