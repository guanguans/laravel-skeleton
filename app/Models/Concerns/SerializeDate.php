<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Models\Concerns;

use Illuminate\Support\Carbon;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait SerializeDate
{
    /**
     * 为数组 / JSON 序列化准备日期。(Laravel 7).
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        // return $date->format($this->dateFormat ?: Carbon::DEFAULT_TO_STRING_FORMAT);
        // return $date->format('Y-m-d H:i:s.vP');
        // return $date->format('Y-m-d H:i:sP');

        /** @var \Illuminate\Support\Carbon $date */
        return $date->inAppTimezone()->format($this->getDateFormat());
    }
}
