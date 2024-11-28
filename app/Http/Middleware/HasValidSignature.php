<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to validate the signature of the request.
 * The signature is a base64 encoded string with the following format:
 * base64_encode("$timestamp$signature")
 * where $timestamp is the current timestamp and $signature is the hash_hmac of the request content.
 *
 * @see https://github.com/nandi95/laravel-starter/blob/main/app/Http/Middleware/HasValidSignature.php
 */
class HasValidSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $givenSignature = $request->header('x-signature');

        // abort if the signature is not present
        abort_if(! \is_string($givenSignature), Response::HTTP_UNAUTHORIZED, 'Invalid signature');

        $decoded = base64_decode($givenSignature, true);

        // abort if the signature is not valid base64
        abort_if($decoded === false, Response::HTTP_UNAUTHORIZED, 'Invalid signature');

        $explodedDecoded = explode('$', $decoded);

        // abort if the signature is not in the correct format
        abort_if(\count($explodedDecoded) !== 2, Response::HTTP_UNAUTHORIZED, 'Invalid signature');

        [$timestamp, $givenSignature] = $explodedDecoded;
        $timestamp = Carbon::parse($timestamp);

        // abort if the timestamp is invalid or older than a minute
        abort_if(
            ! $timestamp->isValid() || $timestamp->isBefore(now()->subMinute()),
            Response::HTTP_UNAUTHORIZED,
            'Invalid signature'
        );

        $validSignature = hash_equals(
            $givenSignature,
            hash_hmac('sha256', $request->getContent(), (string) config('auth.encrypt_pass'))
        );

        // abort if the signature is invalid
        abort_if(! $validSignature, Response::HTTP_UNAUTHORIZED, 'Invalid signature');

        return $next($request);
    }
}
