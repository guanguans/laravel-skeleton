<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * @see https://dev.to/lotyp/laravel-config-problem-is-it-time-for-a-revolution-159f
 * @see https://github.com/lotyp
 * @see https://github.com/wayofdev
 * @see https://x.com/wlotyp
 */
final readonly class AutoWire
{
    /**
     * Create a new AutoWire instance.
     *
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(private string $abstract, private array $parameters = []) {}

    /**
     * Magic method for var_export().
     *
     * @param  array{abstract: string, parameters: array<string, mixed>}  $properties
     */
    public static function __set_state(array $properties): self
    {
        return new self($properties['abstract'], $properties['parameters']);
    }

    /**
     * Resolve the AutoWire instance using the container.
     *
     * @throws BindingResolutionException
     */
    public function resolve(): mixed
    {
        return app()->make($this->abstract, $this->parameters);
    }
}
