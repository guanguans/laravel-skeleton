<?php

/**
 * @routeNamespace("App\Http\Controllers")
 *
 * @routePrefix("")
 */

declare(strict_types=1);

use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Overtrue\LaravelUploader\LaravelUploader;

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
// Route::get('docs', static fn () => view('scribe.index'))->middleware(['verify.production.environment', 'signed'])->name('docs');

// fallback 路由应该始终是应用程序注册的最后一个路由
Route::fallback(static function (): void {
    // redirect 404
    abort(404);
});

LaravelUploader::routes();

Route::middleware('web')->get('up', static function () {
    Event::dispatch(new DiagnosingHealth);

    return View::file(base_path('vendor/laravel/framework/src/Illuminate/Foundation/resources/health-up.blade.php'));
});

/**
 * @see https://caesardev.se/blogg/god-mode-my-most-commonly-used-laravel-snippet
 */
Route::get('acting-as/{id}', static function ($id) {
    abort_unless(app()->isLocal() && app()->hasDebugModeEnabled(), 404);

    \Illuminate\Support\Facades\Auth::loginUsingId($id);

    return redirect('dashboard');
});

/**
 * @see https://www.harrisrafto.eu/enhancing-concurrency-control-with-laravels-session-blocking
 */
Route::post('/order', static function (): void {
    // Order processing logic
})->block($lockSeconds = 5, $waitSeconds = 10);
