<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Support\Carbon;

/**
 * @mixin \Illuminate\Support\Carbon
 */
#[Mixin(Carbon::class)]
class CarbonMixin
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

    /**
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function toFormattedDateTimeString(): \Closure
    {
        return static fn (): Carbon => static::this()->format('Y-m-d H:i:s');
    }
}
