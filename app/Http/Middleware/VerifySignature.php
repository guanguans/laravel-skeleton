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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $secret
     * @param  int  $effectiveTime
     * @param  bool  $checkRepeatRequest
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
            'signature' => 'required|string',
            'nonce' => 'required|string|size:16',
            'timestamp' => sprintf('required|int|max:%s|min:%s', $time = time(), $time - $effectiveTime),
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
        if (! $signer->validate($request->header('signature'), $parameters)) {
            throw new InvalidSignatureException();
        }
    }

    protected function validateRepeatRequest(Request $request, int $effectiveTime)
    {
        $cacheSignature = Cache::get($signature = $request->header('signature'));
        if ($cacheSignature) {
            throw new InvalidRepeatRequestException();
        }

        Cache::put($signature, spl_object_hash($request), $effectiveTime);
    }
}
