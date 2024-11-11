<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

class VerifyFormPassword
{
    public function handle(Request $request, Closure $next, string $name = 'password'): Response
    {
        $request->whenFilled($name, static function () use ($request, $name): void {
            $request->validate([
                $name => Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ]);
        });

        return $next($request);
    }
}
