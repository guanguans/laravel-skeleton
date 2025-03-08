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

namespace App\Support\Traits;

use Illuminate\Support\Str;

/**
 * @property array $casts
 */
trait Castable
{
    protected function cast(array &$source): void
    {
        foreach ($source as $key => &$value) {
            $value = $this->castValue($key, $value);
        }
    }

    protected function castValue(string $key, $value)
    {
        $cast = $this->castKey($key);

        $method = $this->castMethodName($cast);

        return $this->{$method}($value);
    }

    protected function castKey(string $key): string
    {
        return $this->casts[$key] ?? 'default';
    }

    protected function castMethodName(string $key): string
    {
        return (string) Str::of($key)->start('castTo_')->camel();
    }

    protected function castToArray($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (\is_array($value)) {
            return $value;
        }

        parse_str($value, $output);

        return $output;
    }

    protected function castToInteger($value): ?int
    {
        return empty($value) && !is_numeric($value) ? null : $value;
    }

    protected function castToString(?string $value): string
    {
        return (string) $value;
    }

    protected function castToDefault($value)
    {
        return $value;
    }
}
