<?php

/** @noinspection PhpUnhandledExceptionInspection */

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

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Lubusin\Decomposer\Controllers\DecomposerController;
use Overtrue\LaravelUploader\LaravelUploader;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

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

Route::get('/', static fn () => view('welcome'))->name('index');
Route::fallback(static fn () => abort(404))->name('fallback');

LaravelUploader::routes();
Route::get('composer', [DecomposerController::class, 'index']);
Route::get('logs', [LogViewerController::class, 'index']);

/**
 * @see https://caesardev.se/blogg/god-mode-my-most-commonly-used-laravel-snippet
 */
Route::get('acting-as/{id}', static function (int $id) {
    abort_unless(app()->isLocal() && app()->hasDebugModeEnabled(), 404);

    Auth::loginUsingId($id);

    return redirect('dashboard');
})->name('acting-as.id');

/**
 * @see https://www.harrisrafto.eu/enhancing-concurrency-control-with-laravels-session-blocking
 */
Route::post('order', static function (): void {
    // Order processing logic
})->name('order')->block(5, 5);

/**
 * @see https://laravel-news.com/route-shallow-resource
 * @see https://laravel-news.com/route-resource-scoped
 */
Route::resource('order.items', Controller::class)->shallow()->scoped([
    'item' => 'sku',
]);

Route::post('update-password', static function (Request $request) {
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
})->name('update-password');

Route::post('confirm-password', static function (Request $request) {
    if (!Hash::check($request->password, $request->user()?->password)) {
        return back()->withErrors([
            'password' => ['The provided password does not match our records.'],
        ]);
    }

    $request->session()->passwordConfirmed();

    return redirect()->intended();
})->name('confirm-password')->middleware(['auth', 'throttle:6,1']);

Route::delete('account', static function (Request $request) {
    $request->user()->delete();
    Auth::logout();

    return redirect('/')->with('status', 'Your account has been deleted.');
})->name('account.destroy')->middleware(['auth', 'password.confirm']);
