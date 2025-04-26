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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Simple Content Security Policy middleware.
 *
 * Provides a super simple way to add a CSP to a Laravel app.
 * Simply add the required directives to your config/csp.php file.
 * Hooks directly into Vite to generate a Nonce for scripts and styles.
 *
 * !!! Don't forget to add the CSP middleware into the 'web' middleware group !!!
 *
 * @author Stephen Rees-Carter <https://stephenreescarter.net/>
 *
 * @see https://larasec.substack.com/p/in-depth-content-security-policy
 * @see https://gist.github.com/valorin/d4cb9daa190fdee90603efaa8cbc5886
 */
final class CSP
{
    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): (JsonResponse|RedirectResponse|Response) $next
     */
    public function handle(Request $request, \Closure $next): SymfonyResponse
    {
        Vite::useCspNonce();

        $response = $next($request);

        $csp = config('csp');
        $csp['policy']['script-src'][] = "'nonce-".Vite::cspNonce()."'";
        $csp['policy']['style-src'][] = "'nonce-".Vite::cspNonce()."'";

        if (app()->isLocal()) {
            $vite = Vite::asset('');
            $csp['policy']['connect-src'][] = 'wss:'.Str::after($vite, 'https:');
            $csp['policy']['script-src'][] = $vite;
            $csp['policy']['style-src'][] = $vite;
        }

        $policy = collect($csp['policy'])
            ->filter()
            ->map(static fn ($value, $key): string => "$key ".collect($value)->filter()->implode(' '))
            ->implode(' ; ');

        $header = $csp['report_only'] ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';

        $response->headers->set($header, $policy);

        return $response;
    }
}
