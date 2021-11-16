<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 接口文档
Route::get('docs', function () {
    return view('scribe.index');
})->name('docs')->middleware('signed');

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')
    ->middleware([function (\Illuminate\Http\Request $request, $next) {
        abort_if(\Illuminate\Support\Facades\App::isProduction(), 404);

        return $next($request);
    }])->name('logs');
