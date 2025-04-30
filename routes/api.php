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

use App\Http\Middleware\LogHttp;
use App\Http\Middleware\VerifySignature;
use App\Models\HttpLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::get('user', static fn (Request $request) => $request->user())->middleware('auth:sanctum');

Route::fallback(static fn () => abort(404, 'Not Found Api'));

/**
 * @see https://www.harrisrafto.eu/streaming-large-json-datasets-in-laravel-with-streamjson/
 */
Route::get('users.json', static fn () => response()->streamJson([
    'users' => HttpLog::query()->cursor(),
]));

Route::group([
    'as' => 'api.',
    'namespace' => '\App\Http\Controllers\Api',
    'prefix' => 'v1',
    'middleware' => [
        // VerifySignature::with(config('services.signer.default.secret')),
        LogHttp::with('daily'),
    ],
], static function (Router $router): void {
    $router->get('ping/{bad?}', 'PingController')->name('ping');
    $router->middleware('auth:api')->group(static function (Router $router): void {
        $router->group([
            'as' => 'auth.',
            'prefix' => 'auth',
            'controller' => 'AuthController',
        ], static function (Router $router): void {
            /** @see https://www.harrisrafto.eu/laravels-missing-link-customizing-404-responses-for-model-binding */
            $router->get('index', 'index')->name('index')->withoutMiddleware('auth:api');
            $router->get('me', 'me')->name('me')->missing(static fn (Request $request) => to_route('api.auth.index'));
            $router->post('login', 'login')->name('login')->withoutMiddleware('auth:api');
            $router->post('logout', 'logout')->name('logout');
            $router->post('refresh', 'refresh')->name('refresh');
            $router->post('register', 'register')->name('register')->withoutMiddleware('auth:api');
        });
    });
});
