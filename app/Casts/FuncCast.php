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
    private $position;

    /**
     * @var array
     */
    private $args;

    /**
     * @param  string  $name
     * @param ...$args
     */
    public function __construct(string $name, int $position = 0, ...$args)
    {
        $this->name = $name;
        $this->position = $position;
        $this->args = $args;
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

        array_splice($this->args, $this->position, 0, $value);

        return call_user_func($callback, ...$this->args);
    }
}
