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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class LocaleServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        $this->setLocales();
        $this->whenever(Request::getFacadeRoot()?->user()?->locale, function (self $serviceProvider, $locale): void {
            $this->setLocales($locale);
        });
    }

    public function shouldRegister(): bool
    {
        return true;
    }

    private function setLocales(?string $locale = null): void
    {
        $locale and $this->app->setLocale($locale);
        Number::useLocale($this->app->getLocale());
        Carbon::setLocale($this->app->getLocale());
    }
}
