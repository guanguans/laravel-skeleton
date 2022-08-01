<?php

namespace App\Support\Macros;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class RequestMacro
{
    public function userId(): callable
    {
        return function () {
            /** @var \Illuminate\Http\Request $this */
            return optional($this->user())->id;
        };
    }

    public function isAdmin(): callable
    {
        return function () {
            /** @var \Illuminate\Http\Request $this */
            return (bool)optional($this->user())->is_admin;
        };
    }

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

    public function whenRouteIs(): callable
    {
        /** @var string|string[] $patterns */
        return function ($patterns, callable $callback) {
            /** @var \Illuminate\Http\Request $this */
            if ($value = $this->routeIs($patterns)) {
                return $callback($this, $value) ?: $this;
            }

            return $this;
        };
    }

    public function whenIs(): callable
    {
        /** @var string|string[] $patterns */
        return function ($patterns, callable $callback) {
            /** @var \Illuminate\Http\Request $this */
            if ($value = $this->is($patterns)) {
                return $callback($this, $value) ?: $this;
            }

            return $this;
        };
    }

    public function propertyAware(): callable
    {
        return function ($property, $value) {
            /** @var \Illuminate\Http\Request $this */
            if (! property_exists($this, $property)) {
                throw new InvalidArgumentException('The property not exists.');
            }

            app()->has('original_properties') or app()->instance('original_properties', []);
            app()->extend('original_properties', function ($properties) use ($property) {
                isset($properties[$property]) or $properties[$property] = $this->{$property};

                return $properties;
            });

            $this->{$property} = $value;

            return $this;
        };
    }

    public function recoverProperties(): callable
    {
        return function () {
            if (! app()->has('original_properties')) {
                return;
            }

            /** @var \Illuminate\Http\Request $this */
            foreach (app('original_properties') as $property => $value) {
                $this->{$property} = $value;
            }
        };
    }

    public function matchRoute(): callable
    {
        return function ($includingMethod = true) {
            /* @var \Illuminate\Routing\RouteCollection $routeCollection */
            $routeCollection = app(Router::class)->getRoutes();
            /** @var \Illuminate\Http\Request $this */
            $routes = is_null($this->method())
                ? $routeCollection->getRoutes()
                : Arr::get($routeCollection->getRoutesByMethod(), $this->method(), []);
            [$fallbacks, $routes] = collect($routes)->partition(function ($route) {
                return $route->isFallback;
            });

            return $routes->merge($fallbacks)->first(function (Route $route) use ($includingMethod) {
                /** @var \Illuminate\Http\Request $this */
                return $route->matches($this, $includingMethod);
            });
        };
    }
}
