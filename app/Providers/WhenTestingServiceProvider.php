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

use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class WhenTestingServiceProvider extends AggregateServiceProvider implements ShouldRegisterContract
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @noinspection ClassOverridesFieldOfSuperClassInspection
     * @noinspection PropertyInitializationFlawsInspection
     */
    protected $providers = [
    ];

    public function shouldRegister(): bool
    {
        return $this->app->runningUnitTests();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        /** @see \Illuminate\Foundation\Testing\Concerns\InteractsWithTestCaseLifecycle */
        $this->whenever($this->app->environment('testing'), static function (): void {
            // Http::preventStrayRequests(); // Preventing Stray Requests
            // Mail::alwaysTo('taylor@example.com');
            // Carbon::setTestNow('2031-04-05');
            // Carbon::setTestNowAndTimezone('2031-04-05', 'Asia/Shanghai');
            // CarbonImmutable::setTestNow();
            // CarbonImmutable::setTestNowAndTimezone('2031-04-05', 'Asia/Shanghai');
            // ParallelTesting::setUpTestDatabase(static function (string $database, int $token) {
            //     Artisan::call('db:seed');
            // });
        });
    }
}
