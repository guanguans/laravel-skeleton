<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Rules;

use Illuminate\Support\Str;

final class ImeiRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        if (\strlen($value) !== 15 || ! ctype_digit($value)) {
            return false;
        }

        $digits = str_split($value); // Get digits
        $imeiLast = array_pop($digits); // Remove last digit, and store it
        $log = [];

        foreach ($digits as $key => $n) {
            if ($key & 1) {
                $double = str_split($n * 2); // Get double digits
                $n = array_sum($double); // Sum double digits
            }

            $log[] = $n; // Append log
        }

        $sum = array_sum($log) * 9; // Sum log & multiply by 9

        return Str::endsWith($sum, $imeiLast);
    }
}
