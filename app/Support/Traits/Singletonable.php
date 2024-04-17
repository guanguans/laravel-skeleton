<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Traits;

trait Singletonable
{
    protected function __construct(...$parameters) {}

    protected function __clone() {}

    final public function __wakeup(): void {}

    public static function instance(...$parameters)
    {
        app()->singletonIf(static::class, static fn () => new static(...$parameters));

        return app(static::class);
    }
}
