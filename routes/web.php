<?php

/**
 * @routeNamespace("App\Http\Controllers")
 *
 * @routePrefix("")
 */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
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
    abort(404, 'Web page not found.');
});

Route::fallback(static fn (Request $request) => $request->expectsJson()
    ? new JsonResponse(['error' => 'Not Found'], 404)
    : view('errors.404', ['path' => $request->path()]));

// LaravelUploader::routes();

Route::middleware('web')->get('up', static function () {
    Event::dispatch(new DiagnosingHealth);

    return View::file(base_path('vendor/laravel/framework/src/Illuminate/Foundation/resources/health-up.blade.php'));
});

/**
 * @see https://caesardev.se/blogg/god-mode-my-most-commonly-used-laravel-snippet
 */
Route::get('acting-as/{id}', static function (int $id) {
    abort_unless(app()->isLocal() && app()->hasDebugModeEnabled(), 404);

    Auth::loginUsingId($id);

    return redirect('dashboard');
});

/**
 * @see https://www.harrisrafto.eu/enhancing-concurrency-control-with-laravels-session-blocking
 */
Route::post('/order', static function (): void {
    // Order processing logic
})->block($lockSeconds = 5, $waitSeconds = 10);

Route::post('/update-password', static function (Request $request) {
    // Validate the new password
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    // Check the current password
    if (!Hash::check($request->current_password, Auth::user()?->password)) {
        return back()->withErrors(['current_password' => 'The provided password does not match our records.']);
    }

    // Log out other devices
    Auth::logoutOtherDevices($request->current_password);

    // Update the password
    Auth::user()?->update([
        'password' => Hash::make($request->new_password),
    ]);

    return redirect('/dashboard')->with('status', 'Password updated and other devices logged out.');
});

Route::post('/confirm-password', static function (Request $request) {
    if (!Hash::check($request->password, $request->user()?->password)) {
        return back()->withErrors([
            'password' => ['The provided password does not match our records.'],
        ]);
    }

    $request->session()->passwordConfirmed();

    return redirect()->intended();
})->middleware(['auth', 'throttle:6,1']);

Route::delete('/account', static function (Request $request) {
    $request->user()->delete();
    Auth::logout();

    return redirect('/')->with('status', 'Your account has been deleted.');
})->middleware(['auth', 'password.confirm']);
