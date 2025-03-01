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

class Base64Cast implements CastsAttributes
{
    public function __construct(
        private readonly bool $isCastGet = true,
        private readonly bool $isCastSet = false
    ) {}

    /**
     * Cast the given value.
     */
    #[\Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $this->isCastGet ? base64_encode($value) : $value;
    }

    /**
     * Prepare the given value for storage.
     */
    #[\Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $this->isCastSet ? base64_decode($value, true) : $value;
    }
}
