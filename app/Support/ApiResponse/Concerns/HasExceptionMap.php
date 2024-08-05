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

    /**
     * @param  class-string|class-string<\Throwable>  $exception
     * @param  callable(\Throwable): array{
     *     message: string,
     *     code: int,
     *     error: ?array,
     *     headers: array,
     * }|array{
     *     message: string,
     *     code: int,
     *     error: ?array,
     *     headers: array,
     *  }  $map
     */
    public function prependExceptionMap(string $exception, mixed $map): self
    {
        return $this->tapExceptionMap(static function (Collection $exceptionMap) use ($map, $exception): void {
            $exceptionMap->prepend($map, $exception);
        });
    }

    /**
     * @param  class-string|class-string<\Throwable>  $exception
     * @param  callable(\Throwable): array{
     *     message: string,
     *     code: int,
     *     error: ?array,
     *     headers: array,
     * }|array{
     *     message: string,
     *     code: int,
     *     error: ?array,
     *     headers: array,
     *  }  $map
     */
    public function putExceptionMap(string $exception, mixed $map): self
    {
        return $this->tapExceptionMap(static function (Collection $exceptionMap) use ($exception, $map): void {
            $exceptionMap->put($exception, $map);
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
     * @return array{
     *     message: string,
     *     code: int,
     *     error: ?array,
     *     headers: array,
     * }
     */
    private function parseExceptionMap(\Throwable $throwable): array
    {
        $map = $this->exceptionMap->first(
            static fn (mixed $map, string $exception): bool => $throwable instanceof $exception,
            []
        );

        return \is_callable($map) || ! \is_array($map) ? app()->call($map, ['throwable' => $throwable]) : $map;
    }
}
