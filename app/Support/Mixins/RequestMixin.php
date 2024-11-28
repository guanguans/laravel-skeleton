<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

/**
 * @mixin \Illuminate\Http\Request
 */
#[Mixin(Request::class)]
class RequestMixin
{
    public function userId(): callable
    {
        return fn () => $this->user()?->id;
    }

    public function isAdmin(): callable
    {
        return fn (): bool => (bool) $this->user()?->is_admin;
    }

    public static function isAdminDeveloper(): callable
    {
        return static fn (): bool => str(request()->user()?->username)->is(config('services.develop.fingerprints'));
    }

    public function isWechat(): callable
    {
        return fn (): bool => str_contains($this->userAgent(), 'MicroMessenger');
    }

    public function headers(): callable
    {
        return fn ($key = null, $default = null) => null === $key
            ? collect($this->header())
                ->map(static fn ($header) => $header[0])
                ->toArray()
            : $this->header($key, $default);
    }

    public function strictInput(): callable
    {
        return function ($keys = null): array {
            $input = $this->getInputSource()->all();

            if (! $keys) {
                return $input;
            }

            $results = [];

            foreach (\is_array($keys) ? $keys : \func_get_args() as $key) {
                Arr::set($results, $key, Arr::get($input, $key));
            }

            return $results;
        };
    }

    public function strictAll(): callable
    {
        return function ($keys = null) {
            $input = array_replace_recursive($this->strictInput(), $this->allFiles());

            if (! $keys) {
                return $input;
            }

            $results = [];

            foreach (\is_array($keys) ? $keys : \func_get_args() as $key) {
                Arr::set($results, $key, Arr::get($input, $key));
            }

            return $results;
        };
    }

    public function validateStrictAll(): callable
    {
        return fn (array $rules, ...$params) => validator()->validate($this->strictAll(), $rules, ...$params);
    }

    public function validateStrictAllWithBag(): callable
    {
        return function (string $errorBag, array $rules, ...$params): callable {
            try {
                return $this->validateStrictAll($rules, ...$params);
            } catch (ValidationException $validationException) {
                $validationException->errorBag = $errorBag;

                throw $validationException;
            }
        };
    }

    public function whenRouteIs(): callable
    {
        return function ($patterns, callable $callback) {
            if ($value = $this->routeIs($patterns)) {
                return $callback($this, $value) ?: $this;
            }

            return $this;
        };
    }

    public function whenIs(): callable
    {
        return function ($patterns, callable $callback) {
            if ($value = $this->is($patterns)) {
                return $callback($this, $value) ?: $this;
            }

            return $this;
        };
    }

    public function propertyAware(): callable
    {
        return function ($property, $value) {
            throw_unless(property_exists($this, $property), \InvalidArgumentException::class, 'The property not exists.');

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
        return function (): void {
            if (! app()->has('original_properties')) {
                return;
            }

            foreach (app('original_properties') as $property => $value) {
                $this->{$property} = $value;
            }
        };
    }

    public function matchRoute(): callable
    {
        return function ($includingMethod = true) {
            /** @var \Illuminate\Routing\RouteCollection $routeCollection */
            $routeCollection = app(Router::class)->getRoutes();

            $routes = Arr::get($routeCollection->getRoutesByMethod(), $this->method(), []);

            [$fallbacks, $routes] = collect($routes)->partition(static fn ($route) => $route->isFallback);

            return $routes->merge($fallbacks)->first(fn (Route $route) => $route->matches($this, $includingMethod));
        };
    }
}
