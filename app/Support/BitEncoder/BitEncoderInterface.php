<?php

namespace App\Support\BitEncoder;

interface BitEncoderInterface
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
