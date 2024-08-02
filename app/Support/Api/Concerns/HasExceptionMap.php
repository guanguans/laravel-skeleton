<?php

declare(strict_types=1);

namespace App\Support\Api\Concerns;

/**
 * @mixin \App\Support\Api\ApiResponse
 */
trait HasExceptionMap
{
    private array $exceptionMap = [];

    public function getExceptionMap(): array
    {
        return $this->exceptionMap;
    }

    public function setExceptionMap(array $exceptionMap): HasExceptionMap
    {
        $this->exceptionMap = $exceptionMap;

        return $this;
    }

    public function withExceptionMap(string $exception, array $map): self
    {
        $this->exceptionMap[$exception] = $map;

        return $this;
    }
}
