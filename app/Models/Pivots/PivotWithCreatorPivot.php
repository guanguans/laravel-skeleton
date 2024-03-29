<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PivotWithCreatorPivot extends Pivot
{
    public function fill(array $attributes)
    {
        return parent::fill([...$attributes, 'creator_id' => $attributes['creator_id'] ?? auth()->id()]);
    }
}
