<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Throwable;

class CallbackGetCast implements CastsAttributes
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var int
     */
    protected $castingAttributeCallbackArgIndex;

    /**
     * @var array
     */
    protected $remainingCallbackArgs;

    /**
     * @param  string  $callback  The callback(function、class::method、class@method) to be used to cast the attribute.
     * @param  int  $castingAttributeCallbackArgIndex  The index of the argument that will be the attribute being casted.
     * @param  ...$remainingCallbackArgs  These are the remaining callback arguments.
     */
    public function __construct(string $callback, int $castingAttributeCallbackArgIndex = 0, ...$remainingCallbackArgs)
    {
        $this->callback = $this->resolveCallback($callback);
        $this->castingAttributeCallbackArgIndex = $castingAttributeCallbackArgIndex;
        $this->remainingCallbackArgs = $remainingCallbackArgs;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        array_splice($this->remainingCallbackArgs, $this->castingAttributeCallbackArgIndex, 0, $value);

        return \call_user_func($this->callback, ...$this->remainingCallbackArgs);
    }

    /**
     * It takes a string and returns a callable
     *
     * @param  string  $callback  The callback to be executed.
     * @return callable A callable.
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

        if (\count($segments) !== 2 || ! method_exists($segments[0], $segments[1])) {
            throw new InvalidArgumentException("Invalid callback: $callback");
        }

        try {
            return [resolve($segments[0]), $segments[1]];
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException("Invalid callback: $callback({$throwable->getMessage()})", $throwable->getCode(), $throwable);
        }
    }
}
