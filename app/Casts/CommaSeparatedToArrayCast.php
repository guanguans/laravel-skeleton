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

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CommaSeparatedToArrayCast implements CastsAttributes
{
    public bool $withoutObjectCaching = true;

    #[\Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        return $value ? explode(',', $value) : [];
    }

    #[\Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
