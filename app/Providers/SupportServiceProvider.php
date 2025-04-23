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
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\DateFactory;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class SupportServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @see https://github.com/cachethq/cachet
     *
     * @throws \Exception
     */
    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, static function (): void {
            /**
             * PHP 8.3.
             */
            ini_set('json.exceptions', '1');

            /**
             * @see https://www.php.net/manual/zh/numberformatter.parsecurrency.php
             * @see https://zh.wikipedia.org/wiki/ISO_4217
             */
            Number::useCurrency('CNY');
        });
    }

    /**
     * @noinspection LaravelFunctionsInspection
     * @noinspection PhpDeprecationInspection
     *
     * @throws \Exception
     */
    private function never(): void
    {
        $this->whenever(false, function (): void {
            /**
             * @see \Carbon\Laravel\ServiceProvider
             */
            Date::use(CarbonImmutable::class);
            Carbon::serializeUsing(static fn (Carbon $timestamp): string => $timestamp->format('Y-m-d H:i:s'));
            DateFactory::useCallable(
                static fn (mixed $result): mixed => $result instanceof CarbonInterface
                    ? $result->setTimezone(Config::string('app.timezone'))
                    : $result
            );

            /**
             * @see https://masteringlaravel.io/daily/2024-11-13-how-can-you-make-sure-the-environment-is-configured-correctly
             */
            env('DB_HOST', static fn () => throw new \Exception('DB_HOST is missing'));
            Env::getOrFail('DB_HOST');

            Number::useLocale($this->app->getLocale());
            Carbon::setLocale($this->app->getLocale());
        });
    }
}
