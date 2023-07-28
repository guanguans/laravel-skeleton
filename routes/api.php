<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::any('/any', function (Request $request) {
    $request->validate([
        'file_name' => 'file',
    ]);

    return response()->json([
        'method' => $request->method(),
        'headers' => $request->header(),
        'query' => $request->query(),
        'post' => $request->post(),
        'FILES' => $_FILES,
        'files' => $request->file(),
        'cookie' => $request->cookie(),
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([
    'api',
    // sprintf('verify.signature:%s', config('services.signer.default.secret')),
    'log.http',
])->prefix('v1')->namespace('App\Http\Controllers\Api')->group(function (Router $router) {
    Route::middleware([])->group(function (Router $router) {
        Route::match(['GET', 'POST'], 'ping/{is_bad?}', 'PingController@ping')->name('ping');
    });

    Route::middleware(['auth:api'])->group(function (Router $router) {
        Route::prefix('auth')->name('auth.')->group(function (Router $router) {
            Route::post('register', 'AuthController@register')->name('register')->withoutMiddleware(['auth:api']);
            Route::post('login', 'AuthController@login')->name('login')->withoutMiddleware(['auth:api']);
            Route::post('logout', 'AuthController@logout')->name('logout');
            Route::post('refresh', 'AuthController@refresh')->name('refresh');
            Route::get('me', 'AuthController@me')->name('me');
            Route::get('index', 'AuthController@index')->name('index')->withoutMiddleware(['auth:api']);
        });
    });
});
