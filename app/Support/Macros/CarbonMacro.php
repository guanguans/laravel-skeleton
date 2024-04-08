<?php

declare(strict_types=1);

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
