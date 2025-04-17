<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Rules;

use Illuminate\Support\Str;

final class ImeiRule extends Rule
{
    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        if (\strlen($value) !== 15 || !ctype_digit($value)) {
            return false;
        }

        $digits = str_split($value); // Get digits
        $imeiLast = array_pop($digits); // Remove last digit, and store it
        $log = [];

        foreach ($digits as $key => $digit) {
            if ($key & 1) {
                $double = str_split((string) ($digit * 2)); // Get double digits
                $digit = array_sum($double); // Sum double digits
            }

            $log[] = $digit; // Append log
        }

        $sum = array_sum($log) * 9; // Sum log & multiply by 9

        return Str::endsWith($sum, $imeiLast);
    }
}
