<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FuncCast implements CastsInboundAttributes
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $mainArgIndex;

    /**
     * @var array
     */
    private $secondaryArgs;

    /**
     * @param  string  $name
     * @param  int  $mainArgIndex
     * @param ...$secondaryArgs
     */
    public function __construct(string $name, int $mainArgIndex = 0, ...$secondaryArgs)
    {
        $this->name = $name;
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
        $callback = $this->name;
        if (method_exists(Str::class, $this->name)) {
            $callback = [Str::class, $this->name];
        } elseif (method_exists(Arr::class, $this->name)) {
            $callback = [Arr::class, $this->name];
        }

        array_splice($this->secondaryArgs, $this->mainArgIndex, 0, $value);

        return call_user_func($callback, ...$this->secondaryArgs);
    }
}
