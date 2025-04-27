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

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;

/**
 * @mixin \Illuminate\Http\Request
 */
#[Mixin(Request::class)]
final class RequestMixin
{
    public function userId(): \Closure
    {
        return fn () => $this->user()?->id;
    }

    public function isAdmin(): \Closure
    {
        return fn (): bool => (bool) $this->user()?->is_admin;
    }

    public static function isAdminDeveloper(): \Closure
    {
        return static fn (): bool => str(\Illuminate\Support\Facades\Request::getFacadeRoot()->user()?->username)->is(config('services.develop.fingerprints'));
    }

    public function isWechat(): \Closure
    {
        return fn (): bool => str_contains($this->userAgent(), 'MicroMessenger');
    }

    /**
     * @noinspection SensitiveParameterInspection
     */
    public function headers(): \Closure
    {
        return fn (?string $key = null, ?string $default = null) => null === $key
            ? collect($this->header())
                ->map(static fn (array $header) => $header[0])
                ->toArray()
            : $this->header($key, $default);
    }

    public function whenRouteIs(): \Closure
    {
        return function (iterable|string $patterns, callable $callback) {
            if ($value = $this->routeIs($patterns)) {
                return $callback($this, $value) ?: $this;
            }

            return $this;
        };
    }

    public function whenIs(): \Closure
    {
        return function (iterable|string $patterns, callable $callback) {
            if ($value = $this->is($patterns)) {
                return $callback($this, $value) ?: $this;
            }

            return $this;
        };
    }

    public function propertyAware(): \Closure
    {
        return function (string $property, mixed $value): self {
            throw_unless(property_exists($this, $property), \InvalidArgumentException::class, 'The property not exists.');

            app()->has('original_properties') or app()->instance('original_properties', []);
            app()->extend('original_properties', function (array $properties) use ($property): array {
                isset($properties[$property]) or $properties[$property] = $this->{$property};

                return $properties;
            });

            $this->{$property} = $value;

            return $this;
        };
    }

    public function recoverProperties(): \Closure
    {
        return function (): void {
            if (!app()->has('original_properties')) {
                return;
            }

            foreach ((array) app('original_properties') as $property => $value) {
                $this->{$property} = $value;
            }
        };
    }

    /**
     * @noinspection PhpParamsInspection
     */
    public function matchRoute(): \Closure
    {
        return function (bool $includingMethod = true) {
            /** @var \Illuminate\Routing\RouteCollection $routeCollection */
            $routeCollection = app(Router::class)->getRoutes();

            $routes = Arr::get($routeCollection->getRoutesByMethod(), $this->method(), []);

            [$fallbacks, $routes] = collect($routes)->partition(static fn (Route $route) => $route->isFallback);

            return $routes->merge($fallbacks)->first(fn (Route $route) => $route->matches($this, $includingMethod));
        };
    }

    /**
     * @see https://github.com/MrPunyapal/basic-crud/blob/main/app/Traits/HasFileFromUrl.php
     * @see https://github.com/MrPunyapal/basic-crud/blob/main/app/Http/Requests/
     * @see \App\Support\Mixins\UploadedFileMixin::makeFromUrl()
     *
     * @noinspection PhpUndefinedMethodInspection
     */
    public function resolveFileFromUrl(): \Closure
    {
        return function (string $field): void {
            if (!$this->hasFile($field) && filter_var($this->get($field), \FILTER_VALIDATE_URL)) {
                $file = UploadedFile::makeFromUrl((string) $this->string($field));

                if ($file instanceof UploadedFile) {
                    $this->merge([
                        $field => $file,
                    ]);
                }
            }
        };
    }
}
