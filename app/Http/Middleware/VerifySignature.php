<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidRepeatRequestException;
use App\Exceptions\InvalidRequestParameterException;
use App\Support\Signer\HmacSigner;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use TiMacDonald\Middleware\HasParameters;

class VerifySignature
{
    use HasParameters;

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $secret
     * @param  int  $effectiveTime
     * @param  bool  $checkRepeatRequest
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $secret = '', int $effectiveTime = 60, bool $checkRepeatRequest = true)
    {
        $this->validateParams($request, $effectiveTime);

        $this->validateSignature($request, $secret);

        $checkRepeatRequest and $this->validateRepeatRequest($request, $effectiveTime);

        return $next($request);
    }

    protected function validateParams(Request $request, int $effectiveTime)
    {
        $validator = Validator::make([
            'signature' => $request->header('signature'),
            'timestamp' => $request->header('timestamp'),
            'nonce' => $request->header('nonce'),
        ], [
            'signature' => 'required|string',
            'nonce' => 'required|string|size:16',
            'timestamp' => sprintf('required|int|max:%s|min:%s', $time = time(), $time - $effectiveTime),
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestParameterException($validator->errors()->first());
        }
    }

    protected function validateSignature(Request $request, string $secret)
    {
        $params = array_merge($request->input(), [
            'timestamp' => $request->header('timestamp'),
            'nonce' => $request->header('nonce'),
        ]);

        /* @var HmacSigner $signer */
        $signer = app(HmacSigner::class, ['secret' => $secret]);
        if (! $signer->validate($request->header('signature'), $params)) {
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
