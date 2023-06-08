<?php

declare(strict_types=1);

namespace App\Traits;

trait Singletonable
{
    protected function __construct(...$parameters)
    {
    }

    protected function __clone()
    {
    }

    final public function __wakeup()
    {
    }

    public static function instance(...$parameters)
    {
        app()->singletonIf(static::class, function () use ($parameters) {
            return new static(...$parameters);
        });

        return app(static::class);
    }
}
