<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Casts;

class CommaSeparatedToIntegerArrayCast extends CommaSeparatedToArrayCast
{
    public function get(\Illuminate\Database\Eloquent\Model $model, string $key, mixed $value, array $attributes): array
    {
        return array_map(\intval(...), parent::get($model, $key, $value, $attributes));
    }
}
