<?php

namespace App\Support;

/**
 * Helper class to make manipulating binary data easier.
 */
class Bits
{
    /**
     * Returns string representation as string of binary symbols.
     */
    public static function strbin(string $string): string
    {
        $resultString = '';

        foreach (str_split($string) as $character) {
            $resultString .= sprintf('%08b', ord($character));
        }

        return $resultString;
    }

    /**
     * Returns string representation as integer.
     */
    public static function strdec(string $string): int
    {
        return bindec(self::strbin($string));
    }

    /**
     * Returs a decimal number as string of binary symbols.
     */
    public static function decbin(int $integer, int $length = 8): string
    {
        $binary = decbin($integer);

        while (strlen($binary) % $length !== 0) {
            $binary = '0'.$binary;
        }

        return $binary;
    }

    /**
     * Returns string's length in bits.
     */
    public static function strlen(string $string): int
    {
        return strlen($string) * 8;
    }

    /**
     * Rotate provided decimal's bit to the left by specified positions
     */
    public static function decRotateLeft(int $data, int $positions = 1): int
    {
        return ($data << $positions) | ($data >> (32 - $positions));
    }
}
