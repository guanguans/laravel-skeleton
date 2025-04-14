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

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CommaSeparatedToArrayCastUsing implements Castable
{
    /**
     * @param list<mixed> $arguments
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class(...$arguments) implements CastsAttributes {
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
