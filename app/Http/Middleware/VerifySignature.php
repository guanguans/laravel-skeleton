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

use App\Exceptions\InvalidRepeatRequestException;
use App\Support\HmacSigner;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class VerifySignature
{
    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response $next
     *
     * @throws \Throwable
     */
    public function handle(
        Request $request,
        \Closure $next,
        #[\SensitiveParameter]
        string $secret = '',
        int $effectiveTime = 60,
        bool $checkRepeatRequest = true
    ): Response {
        $this->validateCommonParameters($request, $effectiveTime);

        $this->validateSignature($request, $secret);

        $checkRepeatRequest and $this->validateRepeatRequest($request, $effectiveTime);

        return $next($request);
    }

    private function validateCommonParameters(Request $request, int $effectiveTime): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        Validator::make($request->headers(), [
            'signature' => ['required', 'string'],
            'nonce' => ['required', 'string', 'size:16'],
            'timestamp' => \sprintf(
                'required|int|max:%s|min:%s',
                $time = Carbon::now()->timestamp + 1,
                $time - $effectiveTime
            ),
        ])->validate();
    }

    /**
     * @throws \Throwable
     */
    private function validateSignature(Request $request, #[\SensitiveParameter] string $secret): void
    {
        throw_unless(
            (new HmacSigner(secret: $secret))->validate(
                $request->header('signature'),
                array_merge($request->input(), [
                    'timestamp' => $request->header('timestamp'),
                    'nonce' => $request->header('nonce'),
                ])
            ),
            InvalidSignatureException::class
        );
    }

    /**
     * @throws \Throwable
     */
    private function validateRepeatRequest(Request $request, int $effectiveTime): void
    {
        throw_if(
            Cache::get($signature = $request->header('signature')),
            InvalidRepeatRequestException::class
        );

        // Cache::put($signature, $request->fingerprint(), $effectiveTime);
        Cache::put($signature, spl_object_hash($request), $effectiveTime * 60);
    }
}
