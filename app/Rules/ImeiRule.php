<?php

namespace App\Rules;

final class ImeiRule extends Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
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

        return substr($sum, -1) === $imeiLast;
    }
}
