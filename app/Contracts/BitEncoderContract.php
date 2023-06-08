<?php

declare(strict_types=1);

namespace App\Contracts;

interface BitEncoderContract
{
    /**
     * @param  mixed[]  $set
     */
    public function encode(array $set): int;

    /**
     * @return mixed[]
     */
    public function decode(int $value): array;
}
