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

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request as RequestFacade;

/**
 * @mixin \Illuminate\Support\Carbon
 */
#[Mixin(Carbon::class)]
final class CarbonMixin
{
    public function inAppTimezone(): \Closure
    {
        return fn (): self => $this->setTimezone(config('app.timezone'));
    }

    public function inUserTimezone(): \Closure
    {
        return fn (?string $guard = null): self => $this->setTimezone(
            RequestFacade::user($guard)?->timezone()
        );
    }

    public function toFormattedDateTimeString(): \Closure
    {
        return static fn (): string => self::this()->format('Y-m-d H:i:s');
    }
}
