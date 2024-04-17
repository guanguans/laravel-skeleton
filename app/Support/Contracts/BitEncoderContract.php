<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Contracts;

interface BitEncoderContract
{
    /**
     * @param  array<mixed>  $set
     */
    public function encode(array $set): int;

    /**
     * @return array<mixed>
     */
    public function decode(int $value): array;
}
