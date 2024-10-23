<?php

use App\Models\HttpLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

// 可以为 API 定义单独的回退路由
Route::fallback(static function (): void {
    // redirect 404
    abort(404, 'API page not found.');
});

Route::any('/any', static function (Request $request) {
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

Route::middleware('auth:sanctum')->get('/user', static fn (Request $request) => $request->user());

Route::middleware([
    'api',
    // sprintf('verify.signature:%s', config('services.signer.default.secret')),
    \App\Http\Middleware\LogHttp::class.':daily',
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
            Route::get('me', 'AuthController@me')
                ->name('me')
                ->missing(static function (Request $request) {
                    /** @see https://www.harrisrafto.eu/laravels-missing-link-customizing-404-responses-for-model-binding/ */
                    return Redirect::route('index');
                });
            Route::get('index', 'AuthController@index')->name('index')->withoutMiddleware(['auth:api']);
        });
    });
});

/**
 * @see https://www.harrisrafto.eu/streaming-large-json-datasets-in-laravel-with-streamjson/
 */
Route::get('/users.json', static fn () => response()->streamJson([
    'users' => HttpLog::query()->cursor(),
]));
