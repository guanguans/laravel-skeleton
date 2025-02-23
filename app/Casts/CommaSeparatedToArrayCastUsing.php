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

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Model;

class CommaSeparatedToArrayCastUsing implements Castable
{
    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @param  array<int, mixed>  $arguments
     * @return CastsAttributes|CastsInboundAttributes|class-string<CastsAttributes|CastsInboundAttributes>
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class(...$arguments) implements CastsAttributes
        {
            public bool $withoutObjectCaching = true;

            public function __construct(private readonly string $separator = ',') {}

            public function get(Model $model, string $key, mixed $value, array $attributes): array
            {
                return $value ? explode($this->separator, $value) : [];
            }

            public function set(Model $model, string $key, mixed $value, array $attributes): mixed
            {
                return $value;
            }
        };
    }
}
