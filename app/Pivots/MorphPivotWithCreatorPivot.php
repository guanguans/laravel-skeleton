<?php

namespace App\Pivots;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class MorphPivotWithCreatorPivot extends MorphPivot
{
    public function fill(array $attributes)
    {
        return parent::fill(\array_merge($attributes, ['creator_id' => $attributes['creator_id'] ?? \auth()->id()]));
    }
}
