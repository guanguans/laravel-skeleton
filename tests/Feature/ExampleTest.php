<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use App\Console\Commands\InflectorCommand;
use App\Console\Commands\OptimizeAllCommand;
use App\Console\Commands\ShowUnsupportedRequiresCommand;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

it('is console', function (): void {
    classes(
        static fn (string $class, string $file): bool => str($class)->is('App\\Console\\Commands\\*')
            && str($file)->is('*/../../app/Console/Commands/*')
    )
        ->filter(fn (ReflectionClass $reflectionClass): bool => $reflectionClass->isInstantiable())
        ->reject(fn (ReflectionClass $reflectionClass): bool => str($reflectionClass->getName())->is([
            InflectorCommand::class,
            OptimizeAllCommand::class,
            ShowUnsupportedRequiresCommand::class,
        ]))
        ->keys()
        ->merge([RouteListCommand::class])
        // ->dd()
        ->each(fn (string $class) => $this->artisan($class, ['--no-interaction' => true])->assertOk());
})->group(__DIR__, __FILE__);

it('is http', function (): void {
    // $this->get('/')->assertOk();
    $this->get('/')->assertRedirect('routes');

    $this->get('/api/v1/ping')
        // ->ddBody()
        // ->ddHeaders()
        // ->ddJson()
        ->assertOk()
        ->assertJsonStructure();

    $this->get('api/v1/ping?bad=1')
        // ->ddBody()
        // ->ddHeaders()
        // ->ddJson()
        ->assertBadRequest()
        ->assertJsonStructure();
})->group(__DIR__, __FILE__);

it('is all routes', function (): void {
    collect(resolve(Router::class)->getRoutes())
        ->reject(fn (Route $route) => (fn () => $this->isVendorRoute($route))->call(app()->make(RouteListCommand::class)))
        ->each(fn (Route $route): array => array_map(fn (string $method) => $this->{$method}($route->uri()), $route->methods()));
})->throwsNoExceptions()->group(__DIR__, __FILE__);
