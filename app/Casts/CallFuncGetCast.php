<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class CallFuncGetCast implements CastsAttributes
{
    /**
     * @var string
     */
    private $funcName;

    /**
     * @var int
     */
    private $mainArgIndex;

    /**
     * @var array
     */
    private $secondaryArgs;

    /**
     * @var string[]
     */
    private $supportedClasses = [
        \Illuminate\Support\Str::class,
        \Illuminate\Support\Arr::class,
    ];

    /**
     * @param  string  $funcName
     * @param  int  $mainArgIndex
     * @param ...$secondaryArgs
     */
    public function __construct(string $funcName, int $mainArgIndex = 0, ...$secondaryArgs)
    {
        if (
            ! function_exists($funcName) &&
            ! Arr::first($this->supportedClasses, function ($class) use ($funcName) {
                return method_exists($class, $funcName);
            }, false)
        ) {
            throw new InvalidArgumentException('The given function name is not a valid function or method.');
        }

        $this->funcName = $funcName;
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
        $callback = $this->funcName;

        foreach ($this->supportedClasses as $class) {
            if (method_exists($class, $this->funcName)) {
                $callback = [$class, $this->funcName];

                break;
            }
        }

        array_splice($this->secondaryArgs, $this->mainArgIndex, 0, $value);

        return call_user_func($callback, ...$this->secondaryArgs);
    }
}
