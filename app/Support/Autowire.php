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

namespace App\Support;

/**
 * @see https://dev.to/lotyp/laravel-config-problem-is-it-time-for-a-revolution-159f
 * @see https://github.com/lotyp
 * @see https://github.com/wayofdev
 * @see https://x.com/wlotyp
 * @see https://github.com/wayofdev/laravel-cycle-orm-adapter/blob/master/config/cycle.php
 * @see https://github.com/cycle/database/blob/2.x/src/Config/RestoreStateTrait.php
 * @see https://github.com/cycle/database/tree/2.x/src/Config
 * @see \Guanguans\LaravelApiResponse\Support\Traits\SetStateable
 */
final readonly class Autowire
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private string $abstract,
        private array $parameters = []
    ) {}

    /**
     * Magic method for var_export().
     *
     * @param array{abstract: string, parameters: array<string, mixed>} $properties
     */
    public static function __set_state(array $properties): self
    {
        return new self($properties['abstract'], $properties['parameters']);
    }

    /**
     * Resolve the AutoWire instance using the container.
     */
    public function resolve(): mixed
    {
        return resolve($this->abstract, $this->parameters);
    }
}
