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

namespace App\Support\Traits;

use Illuminate\Support\Arr;

/**
 * @see https://github.com/apiato/core/blob/8.x/src/Traits/SanitizerTrait.php
 *
 * @mixin \Illuminate\Http\Request
 * @mixin \Illuminate\Foundation\Http\FormRequest
 */
trait Sanitizeable
{
    /**
     * Sanitizes the data of a request. This is a superior version of php built-in array_filter() as it preserves
     * FALSE and NULL values as well.
     *
     * @param array $fields a list of fields to be checked in the Dot-Notation (e.g., ['data.name', 'data.description'])
     *
     * @throws \Exception
     *
     * @return array an array containing the values if the field was present in the request and the intersection array
     */
    public function sanitizeInput(array $fields): array
    {
        $data = $this->all();

        $inputAsArray = [];
        $fieldsWithDefaultValue = [];

        // create a multidimensional array based on $fields
        // which was submitted as DOT notation (e.g., data.name)
        foreach ($fields as $key => $value) {
            if (\is_string($key)) {
                // save fields with default values
                $fieldsWithDefaultValue[$key] = $value;
                Arr::set($inputAsArray, $key, $value);
            } else {
                Arr::set($inputAsArray, $value, true);
            }
        }

        // check, if the keys exist in both arrays
        $data = $this->recursiveArrayIntersectKey($data, $inputAsArray);

        // set default values if key doesn't exist
        foreach ($fieldsWithDefaultValue as $key => $value) {
            $data = Arr::add($data, $key, $value);
        }

        return $data;
    }

    /**
     * Recursively intersects 2 arrays based on their keys.
     *
     * @noinspection PhpVariableNamingConventionInspection
     *
     * @param array $a first array (that keeps the values)
     * @param array $b second array to be compared with
     *
     * @return array an array containing all keys that are present in $a and $b. Only values from $a are returned
     */
    private function recursiveArrayIntersectKey(array $a, array $b): array
    {
        $a = array_intersect_key($a, $b);

        foreach ($a as $key => &$value) {
            if (\is_array($value) && \is_array($b[$key])) {
                $value = $this->recursiveArrayIntersectKey($value, $b[$key]);
            }
        }

        return $a;
    }
}
