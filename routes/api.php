<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([
    'api',
    // sprintf('signatured:%s', config('services.signer.default.secret')),
])->prefix('v1')->namespace('App\Http\Controllers\Api')->group(function (Router $router) {
    Route::middleware([])->group(function (Router $router) {
        Route::match(['GET', 'POST'], 'ping/{is_bad?}', 'PingController@ping')->name('ping.ping');
    });

    Route::middleware(['auth:api'])->group(function (Router $router) {
        Route::prefix('auth')->group(function (Router $router) {
            Route::post('login', 'AuthController@login')->name('auth.login')->withoutMiddleware('auth:api');
            Route::post('logout', 'AuthController@logout')->name('auth.logout');
            Route::post('refresh', 'AuthController@refresh')->name('auth.refresh');
            Route::get('me', 'AuthController@me')->name('auth.me');
            Route::get('index', 'AuthController@index')->name('auth.index')->withoutMiddleware('auth:api');
        });
    });
});
