<?php

use App\Http\Middleware\VerifySignature;
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

Route::middleware(['api'])->prefix('v1')->namespace('App\Http\Controllers\Api')->group(function (Router $router) {
    Route::middleware([
        VerifySignature::with([
            'secret' => config('services.signer.default.secret'),
        ]),
    ])->group(function (Router $router) {
        Route::any('ping', 'PingController@ping');
    });
});
