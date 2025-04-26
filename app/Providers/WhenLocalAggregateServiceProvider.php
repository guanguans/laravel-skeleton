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

namespace App\Providers;

use Guanguans\LaravelSoar\SoarServiceProvider;
use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Traits\Conditionable;

final class WhenLocalAggregateServiceProvider extends AggregateServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /** {@inheritDoc} */
    protected $providers = [
        SoarServiceProvider::class,
    ];

    public function register(): void
    {
        if ($this->app->isLocal()) {
            parent::register();
        }
    }

    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, static function (): void {});
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {});
    }
}
