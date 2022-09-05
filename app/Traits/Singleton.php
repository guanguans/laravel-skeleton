<?php

namespace App\Traits;

trait Singleton
{
    public static function instance(...$parameters)
    {
        app()->singletonIf(static::class, function () use ($parameters) {
            return new static(...$parameters);
        });

        return app(static::class);
    }

    protected function __construct(...$parameters)
    {
    }

    protected function __clone()
    {
    }

    protected function __wakeup()
    {
    }
}
