<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * @see https://github.com/laravel/laravel/blob/9.x/app/Http/Controllers/Controller.php
 * @see https://github.com/laravel/laravel/blob/10.x/app/Http/Controllers/Controller.php
 */
class Controller extends BaseController // implements HasMiddleware
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    // public static function middleware(): array
    // {
    //     return [
    //         'auth',
    //         new Middleware('log', only: ['index']),
    //         new Middleware('subscribed', except: ['store']),
    //     ];
    // }
}
