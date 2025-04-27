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

use App\Support\Clients\PushDeer;
use App\Support\Managers\ElasticsearchManager;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

final class SupportServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }
    public array $bindings = [];
    public array $singletons = [
        ElasticsearchManager::class,
    ];

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function register(): void
    {
        $this->registerPushDeer();
    }

    /**
     * @see https://github.com/cachethq/cachet
     * @see https://github.com/LaravelDaily/laravel-tips
     * @see https://github.com/OussamaMater/Laravel-Tips
     *
     * @throws \Exception
     */
    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    /**
     * @noinspection SenselessMethodDuplicationInspection
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function when(): array
    {
        return [];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function provides(): array
    {
        return [
            PushDeer::class,
            ElasticsearchManager::class,
        ];
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

    private function never(): void
    {
        $this->whenever(false, function (): void {
            /**
             * @see \Carbon\Laravel\ServiceProvider
             */
            Date::use(CarbonImmutable::class);
            Date::useCallable(
                static fn (mixed $result): mixed => $result instanceof CarbonInterface
                    ? $result->setTimezone(Config::string('app.timezone'))
                    : $result
            );

            /**
             * @see https://masteringlaravel.io/daily/2024-11-13-how-can-you-make-sure-the-environment-is-configured-correctly
             */
            // env(
            //     'DB_HOST',
            //     static fn () => throw new \RuntimeException('The environment variable [DB_HOST] has no value.')
            // );
            Env::getOrFail('DB_HOST');

            Number::useLocale($this->app->getLocale());
            Carbon::setLocale($this->app->getLocale());
        });
    }

    private function registerPushDeer(): void
    {
        $this->app->singleton(PushDeer::class, static fn (): PushDeer => new PushDeer(config('services.pushdeer')));
    }
}
