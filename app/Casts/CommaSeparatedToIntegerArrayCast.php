<?php

declare(strict_types=1);

namespace App\Casts;

class CommaSeparatedToIntegerArrayCast extends CommaSeparatedToArrayCast
{
    public function get($model, $key, $value, $attributes): array
    {
        return array_map('\intval', parent::get($model, $key, $value, $attributes));
    }
}
