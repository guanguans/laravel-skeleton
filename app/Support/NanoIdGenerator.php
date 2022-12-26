<?php

namespace App\Support;

/**
 * @see https://github.com/vanilophp/framework/blob/master/src/Support/Generators/NanoIdGenerator.php
 */
class NanoIdGenerator
{
    private const ALPHABET = '_-0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private $alphabet;

    private $size = 21;

    public function __construct(int $size = null, string $alphabet = null)
    {
        $this->alphabet = $alphabet ?? self::ALPHABET;
        $this->size = $size ?? $this->size;
    }

    public function generate(): string
    {
        $len = strlen($this->alphabet);
        $mask = (2 << log($len - 1) / M_LN2) - 1;
        $step = (int) ceil(1.6 * $mask * $this->size / $len);
        $id = '';
        while (true) {
            $bytes = $this->random($step);
            for ($i = 1; $i <= $step; $i++) {
                $byte = $bytes[$i] & $mask;
                if (isset($this->alphabet[$byte])) {
                    $id .= $this->alphabet[$byte];
                    if (strlen($id) === $this->size) {
                        return $id;
                    }
                }
            }
        }
    }

    protected function random($size)
    {
        return unpack('C*', \random_bytes($size));
    }
}
