<?php

namespace App\Contracts;

interface BitEncoderContract
{
    /**
     * @param  mixed[]  $set
     * @return int
     */
    public function encode(array $set): int;

    /**
     * @param  int  $value
     * @return mixed[]
     */
    public function decode(int $value): array;
}
