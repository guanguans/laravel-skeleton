<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Providers;

use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Support\AggregateServiceProvider;

class WhenLocalServiceProvider extends AggregateServiceProvider implements ShouldRegisterContract
{
    /** @noinspection PhpFullyQualifiedNameUsageInspection */
    protected $providers = [
        // \Guanguans\LaravelSoar\SoarServiceProvider::class,
    ];

    public function shouldRegister(): bool
    {
        return !$this->app->isLocal();
    }
}
