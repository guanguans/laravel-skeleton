<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Macros;

use Illuminate\Support\Carbon;

/**
 * @mixin \Illuminate\Support\Carbon
 */
class CarbonMacro
{
    public function inAppTimezone(): \Closure
    {
        return fn (): Carbon => $this->setTimezone(config('app.timezone'));
    }

    public function inUserTimezone(): \Closure
    {
        return fn (?string $guard = null): Carbon => $this->setTimezone(
            auth($guard)->user()?->timezone ?? config('app.timezone')
        );
    }
}
