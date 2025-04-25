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

use Carbon\CarbonImmutable;
use Faker\Generator;
use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Traits\Conditionable;

class WhenTestingAggregateServiceProvider extends AggregateServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function register(): void
    {
        if ($this->app->runningUnitTests()) {
            parent::register();
        }
    }

    /**
     * @see \Illuminate\Foundation\Testing\Concerns\InteractsWithTestCaseLifecycle
     */
    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, function (): void {
            $this->whenTesting();
        });
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {});
    }

    private function whenTesting(): void
    {
        $this->whenever($this->app->runningUnitTests(), function (): void {
            Carbon::setTestNow();
            Carbon::setTestNowAndTimezone();
            CarbonImmutable::setTestNow();
            CarbonImmutable::setTestNowAndTimezone();
            Http::preventStrayRequests();
            Mail::alwaysTo('example@example.com');
            ParallelTesting::setUpTestDatabase(static function (): void {
                Artisan::call('db:seed');
            });
            $this->extendFaker();
        });
    }

    private function extendFaker(): void
    {
        $this->app->resolving(static function (mixed $object): void {
            if ($object instanceof Generator) {
                $object->addProvider(
                    new class {
                        public function imageUrl(int $width = 640, int $height = 480): string
                        {
                            return \sprintf('https://placekitten.com/%d/%d', $width, $height);
                        }

                        /**
                         * @param string $format string<'raw', 'full', 'small', 'thumb', 'regular', 'small_s3'>
                         */
                        public function imageRandomUrl(string $format = 'small'): string
                        {
                            return \sprintf('https://random.danielpetrica.com/api/random?format=%s', $format);
                        }
                    }
                );
            }
        });
    }
}
