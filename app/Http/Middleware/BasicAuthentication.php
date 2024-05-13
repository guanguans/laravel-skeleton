<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BasicAuthentication
{
    public function handle(Request $request, \Closure $next): Response
    {
        if (! $request->hasHeader('Authorization')) {
            // Display login prompt
            header('WWW-Authenticate: Basic realm="Unauthorized"');
            abort(\Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
        }

        // Provided username or password does not match, throw an exception
        // Alternatively, the login prompt can be displayed once more
        abort_unless(
            $this->validateCredentials(explode(':', base64_decode(substr($request->header('Authorization'), 6)))),
            \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED
        );

        return $next($request);
    }

    /**
     * @noinspection SensitiveParameterInspection
     */
    private function validateCredentials(array $credentials): bool
    {
        // throw new RuntimeException('Not implemented');
        return (bool) $credentials;
    }
}
