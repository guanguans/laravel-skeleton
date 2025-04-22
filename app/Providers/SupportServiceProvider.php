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
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\DateFactory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class SupportServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @see https://github.com/cachethq/cachet
     */
    public function boot(): void
    {
        $this->whenever(true, static function (): void {
            // $this->booting($this->app->make(SetRequestIdListener::class)->handle(...));
        });

        $this->whenever(true, static function (): void {
            // ini_set('json.exceptions', '1'); // PHP 8.3
            // @see https://www.php.net/manual/zh/numberformatter.parsecurrency.php
            // @see https://zh.wikipedia.org/wiki/ISO_4217
            Number::useCurrency('CNY');
            // @see \Carbon\Laravel\ServiceProvider
            // Carbon::serializeUsing(static fn (Carbon $timestamp): string => $timestamp->format('Y-m-d H:i:s'));
            Date::use(CarbonImmutable::class);
            DateFactory::useCallable(
                static fn (mixed $result): mixed => $result instanceof CarbonInterface
                    ? $result->setTimezone(Config::string('app.timezone'))
                    : $result
            );
            // @see https://masteringlaravel.io/daily/2024-11-13-how-can-you-make-sure-the-environment-is-configured-correctly
            // env('DB_HOST', fn () => throw new \Exception('DB_HOST is missing'));
            // Env::getOrFail('DB_HOST');
        });
    }

    public function shouldRegister(): bool
    {
        return true;
    }
}
