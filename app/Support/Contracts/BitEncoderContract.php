<?php

declare(strict_types=1);

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
