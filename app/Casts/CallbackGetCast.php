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

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CallbackGetCast implements CastsAttributes
{
    /** @var callable */
    protected $callback;
    protected array $remainingCallbackArgs;

    /**
     * @param string $callback the callback(function、class::method、class@method) to be used to cast the attribute
     * @param int $castingAttributeCallbackArgIndex the index of the argument that will be the attribute being cast
     * @param scalar ...$remainingCallbackArgs The remaining callback arguments.
     *
     * @throws \Throwable
     */
    public function __construct(
        string $callback,
        protected int $castingAttributeCallbackArgIndex = 0,
        ...$remainingCallbackArgs
    ) {
        $this->callback = $this->resolveCallback($callback);
        $this->remainingCallbackArgs = $remainingCallbackArgs;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        array_splice($this->remainingCallbackArgs, $this->castingAttributeCallbackArgIndex, 0, $value);

        return \call_user_func($this->callback, ...$this->remainingCallbackArgs);
    }

    /**
     * @see \Illuminate\Container\Container::call()
     * @see https://github.com/PHP-DI/Invoker/blob/master/src/CallableResolver.php
     *
     * @throws \Throwable
     */
    protected function resolveCallback(string $callback): callable
    {
        if (\is_callable($callback)) {
            return $callback;
        }

        $segments = explode('@', $callback, 2);

        if (\is_callable($segments)) {
            return $segments;
        }

        throw_if(
            \count($segments) !== 2 || !method_exists($segments[0], $segments[1]),
            \InvalidArgumentException::class,
            "Invalid callback: $callback"
        );

        try {
            return [resolve($segments[0]), $segments[1]];
        } catch (\Throwable $throwable) {
            throw new \InvalidArgumentException(
                "Invalid callback: $callback({$throwable->getMessage()})",
                $throwable->getCode(),
                $throwable
            );
        }
    }
}
