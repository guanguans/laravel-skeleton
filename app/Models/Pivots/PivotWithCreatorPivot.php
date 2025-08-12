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

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

final class PivotWithCreatorPivot extends Pivot
{
    use HasFactory;

    public function fill(array $attributes): self
    {
        return parent::fill([...$attributes, 'creator_id' => $attributes['creator_id'] ?? auth()->id()]);
    }
}
