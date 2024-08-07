<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidRepeatRequestException;
use App\Support\HmacSigner;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class VerifySignature
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $secret = '', int $effectiveTime = 60, bool $checkRepeatRequest = true)
    {
        $this->validateParameters($request, $effectiveTime);

        $this->validateSignature($request, $secret);

        $checkRepeatRequest and $this->validateRepeatRequest($request, $effectiveTime);

        return $next($request);
    }

    protected function validateParameters(Request $request, int $effectiveTime)
    {
        Validator::make($request->headers(), [
            'signature' => ['required', 'string'],
            'nonce' => ['required', 'string', 'size:16'],
            'timestamp' => sprintf('required|int|max:%s|min:%s', $time = time() + 1, $time - $effectiveTime),
        ])->validate();
    }

    protected function validateSignature(Request $request, string $secret)
    {
        $parameters = array_merge($request->input(), [
            'timestamp' => $request->header('timestamp'),
            'nonce' => $request->header('nonce'),
        ]);

        /** @var HmacSigner $signer */
        $signer = app(HmacSigner::class, ['secret' => $secret]);
        throw_unless($signer->validate($request->header('signature'), $parameters), InvalidSignatureException::class);
    }

    protected function validateRepeatRequest(Request $request, int $effectiveTime)
    {
        $cacheSignature = Cache::get($signature = $request->header('signature'));
        throw_if($cacheSignature, InvalidRepeatRequestException::class);

        // Cache::put($signature, $request->fingerprint(), $effectiveTime);
        Cache::put($signature, spl_object_hash($request), $effectiveTime);
    }
}
