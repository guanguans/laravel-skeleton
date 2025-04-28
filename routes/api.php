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
use App\Models\HttpLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::get('/user', static fn (Request $request) => $request->user())->middleware('auth:sanctum');

Route::fallback(static function (): void {
    abort(404, 'Not Found Api');
});

/**
 * @see https://www.harrisrafto.eu/streaming-large-json-datasets-in-laravel-with-streamjson/
 */
Route::get('/users.json', static fn () => response()->streamJson([
    'users' => HttpLog::query()->cursor(),
]));

Route::middleware([
    'api',
    // sprintf('verify.signature:%s', config('services.signer.default.secret')),
    // LogHttp::class.':daily',
])->scopeBindings()->prefix('v1')->namespace('App\Http\Controllers\Api')->group(static function (Router $router): void {
    Route::middleware([])->group(static function (Router $router): void {
        Route::match(['GET', 'POST'], 'ping/{is_bad?}', 'PingController@ping')->name('ping');
    });
    Route::middleware(['auth:api'])->group(static function (Router $router): void {
        Route::prefix('auth')->name('auth.')->group(static function (Router $router): void {
            Route::post('register', 'AuthController@register')->name('register')->withoutMiddleware(['auth:api']);
            Route::post('login', 'AuthController@login')->name('login')->withoutMiddleware(['auth:api']);
            Route::post('logout', 'AuthController@logout')->name('logout');
            Route::post('refresh', 'AuthController@refresh')->name('refresh');

            /** @see https://www.harrisrafto.eu/laravels-missing-link-customizing-404-responses-for-model-binding */
            Route::get('me', 'AuthController@me')
                ->name('me')
                ->missing(static fn (Request $request) => to_route('index'));
            Route::get('index', 'AuthController@index')->name('index')->withoutMiddleware(['auth:api']);
        });
    });
});
