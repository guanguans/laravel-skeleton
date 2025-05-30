<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Http\Middleware;

use App\Support\Traits\WithPipeArgs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @see https://github.com/MGeurts/genealogy/blob/main/app/Http/Middleware/SetLocale.php
 */
final class SetLocale
{
    use WithPipeArgs;

    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     */
    public function handle(Request $request, \Closure $next, string $locale): SymfonyResponse
    {
        // $locale = auth()->user()?->locale() and app()->setLocale($locale);
        Config::set('app.locale', $locale);
        app()->setLocale($locale);
        Carbon::setLocale($locale);
        Date::setLocale($locale);

        return $next($request);
    }
}
