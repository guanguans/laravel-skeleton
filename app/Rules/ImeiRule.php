<?php

namespace App\Rules;

final class ImeiRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        if (\strlen($value) != 15 || ! ctype_digit($value)) {
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

        return \Illuminate\Support\Str::endsWith($sum, $imeiLast);
    }
}
