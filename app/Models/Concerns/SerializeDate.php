<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Models\Concerns;

use Carbon\Carbon;

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

        return $date->format($this->getDateFormat());
    }
}
