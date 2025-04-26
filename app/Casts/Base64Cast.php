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

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

final readonly class Base64Cast implements CastsAttributes
{
    public function __construct(
        private bool $isCastGet = true,
        private bool $isCastSet = false
    ) {}

    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $this->isCastGet ? base64_encode($value) : $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $this->isCastSet ? base64_decode($value, true) : $value;
    }
}
