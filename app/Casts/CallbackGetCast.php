<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Str;
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
    protected $mainArgIndex;

    /**
     * @var array
     */
    protected $secondaryArgs;

    /**
     * @param  string  $callback [function, class::method, class@method]
     * @param  int  $mainArgIndex
     * @param ...$secondaryArgs
     */
    public function __construct(string $callback, int $mainArgIndex = 0, ...$secondaryArgs)
    {
        $this->callback = $this->resolveCallback($callback);
        $this->mainArgIndex = $mainArgIndex;
        $this->secondaryArgs = $secondaryArgs;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
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
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        array_splice($this->secondaryArgs, $this->mainArgIndex, 0, $value);

        return call_user_func($this->callback, ...$this->secondaryArgs);
    }

    /**
     * @param  string  $callback
     *
     * @return callable
     * @throws \InvalidArgumentException
     */
    protected function resolveCallback(string $callback): callable
    {
        if (is_callable($callback)) {
            return $callback;
        }

        if (! Str::contains($callback, '@')) {
            throw new InvalidArgumentException("Invalid callback: $callback");
        }

        $segments = explode('@', $callback);
        if (is_callable($segments)) {
            return $segments;
        }

        if (count($segments) !== 2 || ! method_exists($segments[0], $segments[1])) {
            throw new InvalidArgumentException("Invalid callback: $callback");
        }

        try {
            return [resolve($segments[0]), $segments[1]];
        } catch (Throwable $e) {
            throw new InvalidArgumentException("Invalid callback: $callback");
        }
    }
}
