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

interface SignerContract
{
    public function sign(array $payload): string;

    public function validate(string $signature, array $payload): bool;
}
