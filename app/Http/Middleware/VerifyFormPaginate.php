<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyFormPaginate
{
    public function handle(
        Request $request,
        Closure $next,
        int $maxPerPage = 100,
        string $perPageName = 'per_page',
    ): Response {
        $request->whenFilled($perPageName, static function () use ($request, $perPageName, $maxPerPage): void {
            $request->validate([
                $perPageName => "max:$maxPerPage",
            ]);
        });

        return $next($request);
    }
}
