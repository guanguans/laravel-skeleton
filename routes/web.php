<?php

declare(strict_types=1);

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

Route::get('/', static fn () => view('welcome'));

// 接口文档
Route::get('docs', static fn () => view('scribe.index'))->middleware(['verify.production.environment', 'signed'])->name('docs');

// fallback 路由应该始终是应用程序注册的最后一个路由
Route::fallback(static function (): void {
    // redirect 404
});
