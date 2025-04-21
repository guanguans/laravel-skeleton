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

class WhenTestingServiceProvider extends AggregateServiceProvider implements ShouldRegisterContract
{
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
}
