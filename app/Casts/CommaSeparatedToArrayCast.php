<?php

declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CommaSeparatedToArrayCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return $value ? explode(',', $value) : [];
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
