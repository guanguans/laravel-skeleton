<?php

declare(strict_types=1);

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
        return $date->format($this->getDateFormat());
    }
}
