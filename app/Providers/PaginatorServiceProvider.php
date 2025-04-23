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

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class PaginatorServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
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
        $this->whenever(false, static function (): void {
            Paginator::defaultSimpleView('pagination::simple-bulma');
            Paginator::defaultView('pagination::bulma');
            Paginator::useBootstrap();
            Paginator::useBootstrapFive();
            Paginator::useBootstrapFour();
        });
    }
}
