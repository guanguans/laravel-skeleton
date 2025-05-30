<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Traits;

trait Singletonable
{
    protected function __construct(...$parameters) {}

    protected function __clone() {}

    final public function __wakeup(): void {}

    public static function instance(mixed ...$parameters): object
    {
        app()->singletonIf(static::class, static fn (): static => new static(...$parameters));

        return app(static::class);
    }
}
