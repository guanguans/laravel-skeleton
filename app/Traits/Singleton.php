<?php

namespace App\Traits;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;

trait Singleton
{
    public static function instance(...$parameters)
    {
        App::singletonIf(static::class, function (Application $app) use ($parameters) {
            return new static(...$parameters);
        });

        return App::make(static::class);
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
