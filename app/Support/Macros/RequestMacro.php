<?php

namespace App\Support\Macros;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class RequestMacro
{
    public function headers(): callable
    {
        return function ($key = null, $default = null) {
            /** @var \Illuminate\Http\Request $this */
            if (is_null($key)) {
                return collect($this->header())
                    ->map(function ($header) {
                        return $header[0];
                    })
                    ->toArray();
            }

            return $this->header($key, $default);
        };
    }

    public function strictInput(): callable
    {
        return function ($keys = null) {
            /** @var \Illuminate\Http\Request $this */
            $input = $this->getInputSource()->all();

            if (! $keys) {
                return $input;
            }

            $results = [];

            foreach (is_array($keys) ? $keys : func_get_args() as $key) {
                Arr::set($results, $key, Arr::get($input, $key));
            }

            return $results;
        };
    }

    public function strictAll(): callable
    {
        return function ($keys = null) {
            /** @var \Illuminate\Http\Request $this */
            $input = array_replace_recursive($this->strictInput(), $this->allFiles());

            if (! $keys) {
                return $input;
            }

            $results = [];

            foreach (is_array($keys) ? $keys : func_get_args() as $key) {
                Arr::set($results, $key, Arr::get($input, $key));
            }

            return $results;
        };
    }

    public function validateStrictAll(): callable
    {
        return function (array $rules, ...$params) {
            /** @var \Illuminate\Http\Request $this */
            return validator()->validate($this->strictAll(), $rules, ...$params);
        };
    }

    public function validateStrictAllWithBag(): callable
    {
        return function (string $errorBag, array $rules, ...$params) {
            /** @var \Illuminate\Http\Request $this */
            try {
                return $this->validateStrictAll($rules, ...$params);
            } catch (ValidationException $e) {
                $e->errorBag = $errorBag;

                throw $e;
            }
        };
    }
}
