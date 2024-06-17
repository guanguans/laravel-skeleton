<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;

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
class CSP
{
    public function handle($request, Closure $next)
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
            ->map(static fn ($value, $key): string => "{$key} ".collect($value)->filter()->implode(' '))
            ->implode(' ; ');

        $header = $csp['report_only'] ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';

        $response->headers->set($header, $policy);

        return $response;
    }
}
