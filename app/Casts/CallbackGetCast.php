<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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
     * @param  string  $callback  the callback(function、class::method、class@method) to be used to cast the attribute
     * @param  int  $castingAttributeCallbackArgIndex  the index of the argument that will be the attribute being casted
     * @param  ...$remainingCallbackArgs  These are the remaining callback arguments.
     */
    public function __construct(string $callback, protected int $castingAttributeCallbackArgIndex = 0, ...$remainingCallbackArgs)
    {
        $this->callback = $this->resolveCallback($callback);
        $this->remainingCallbackArgs = $remainingCallbackArgs;
    }

    /**
     * Prepare the given value for storage.
     */
    #[\Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     */
    #[\Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        array_splice($this->remainingCallbackArgs, $this->castingAttributeCallbackArgIndex, 0, $value);

        return \call_user_func($this->callback, ...$this->remainingCallbackArgs);
    }

    /**
     * It takes a string and returns a callable
     *
     * @param  string  $callback  the callback to be executed
     * @return callable a callable
     *
     * @throws \InvalidArgumentException
     */
    protected function resolveCallback(string $callback): callable
    {
        if (\is_callable($callback)) {
            return $callback;
        }

        /** @var array $segments */
        $segments = explode('@', $callback, 2);
        if (\is_callable($segments)) {
            return $segments;
        }

        throw_if(\count($segments) !== 2 || ! method_exists($segments[0], $segments[1]), \InvalidArgumentException::class, "Invalid callback: $callback");

        try {
            return [resolve($segments[0]), $segments[1]];
        } catch (\Throwable $throwable) {
            throw new \InvalidArgumentException("Invalid callback: $callback({$throwable->getMessage()})", $throwable->getCode(), $throwable);
        }
    }
}
