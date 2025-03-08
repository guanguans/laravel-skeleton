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

namespace App\Casts;

use Illuminate\Database\Eloquent\Model;

class CommaSeparatedToIntegerArrayCast extends CommaSeparatedToArrayCast
{
    #[\Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        return array_map(\intval(...), parent::get($model, $key, $value, $attributes));
    }
}
