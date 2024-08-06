<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Concerns;

use Illuminate\Support\Collection;

/**
 * @mixin \App\Support\ApiResponse\ApiResponse
 */
trait HasExceptionMap
{
    private Collection $exceptionMap;

    public function prependExceptionMap(string $exception, mixed $mapper): self
    {
        return $this->tapExceptionMap(static function (Collection $exceptionMap) use ($mapper, $exception): void {
            $exceptionMap->prepend($mapper, $exception);
        });
    }

    public function putExceptionMap(string $exception, mixed $mapper): self
    {
        return $this->tapExceptionMap(static function (Collection $exceptionMap) use ($exception, $mapper): void {
            $exceptionMap->put($exception, $mapper);
        });
    }

    public function extendExceptionMap(callable $callback): self
    {
        $this->exceptionMap = $callback($this->exceptionMap);

        return $this;
    }

    public function tapExceptionMap(callable $callback): self
    {
        tap($this->exceptionMap, $callback);

        return $this;
    }

    /**
     * @see \Illuminate\Foundation\Exceptions\Handler::mapException()
     *
     * @return \Throwable|array{
     *     message: string,
     *     code: int,
     *     error: ?array,
     *     headers: array,
     * }
     */
    private function mapException(\Throwable $throwable): array|\Throwable
    {
        $mapper = $this->exceptionMap->first(
            static fn (mixed $mapper, string $exception): bool => $throwable instanceof $exception,
            []
        );

        return \is_callable($mapper) || ! (\is_array($mapper) || $mapper instanceof \Throwable)
            ? app()->call($mapper, ['throwable' => $throwable])
            : $mapper;
    }
}
