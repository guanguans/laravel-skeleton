<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Providers;

use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Traits\Conditionable;

final class UnlessProductionAggregateServiceProvider extends AggregateServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @noinspection ClassConstantCanBeUsedInspection
     * @noinspection ClassnameLiteralInspection
     * @noinspection ClassOverridesFieldOfSuperClassInspection
     * @noinspection SpellCheckingInspection
     */
    protected $providers = [
        'Dedoc\\Scramble\\ScrambleServiceProvider',
        'Guanguans\\LaravelSoar\\SoarServiceProvider',
        'JMac\\Testing\\AdditionalAssertionsServiceProvider',
        'Laravel\\Pail\\PailServiceProvider',
        'Laravel\\Telescope\\TelescopeServiceProvider',
        'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
        'PrettyRoutes\\ServiceProvider',
    ];

    public function register(): void
    {
        if (!$this->app->isProduction()) {
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
        $this->whenever(true, static function (): void {
            Mail::alwaysTo('example@example.com');
        });
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {});
    }
}
