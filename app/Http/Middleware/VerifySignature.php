<?php

namespace App\Http\Middleware;

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
     * @var string[]
     */
    protected $rules = [
        'signature' => 'required|string',
        'timestamp' => 'required|int',
        'nonce' => 'required|string|size:16',
    ];

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
        // 参数
        $this->validateParams([
            'signature' => $signature = $request->header('signature'),
            'timestamp' => $timestamp = $request->header('timestamp'),
            'nonce'     => $nonce = $request->header('nonce'),
        ]);

        // 有效期
        $this->validateEffectiveTime($timestamp, $effectiveTime);

        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            $params = $request->query();
        } else {
            $params = $request->post();
        }

        $params['timestamp'] = $timestamp;
        $params['nonce'] = $nonce;

        /* @var HmacSigner $signer */
        $signer = app(HmacSigner::class, [$secret]);
        // dd($signer->sign($params));
        if (!$signer->validate($signature, $params)) {
            throw new InvalidSignatureException();
        }

        // 重放
        $checkRepeatRequest and $this->validateRepeatRequest($signature, $params, $effectiveTime);

        return $next($request);
    }

    protected function validateParams(array $params)
    {
        $validator = Validator::make($params, $this->rules);
        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }

    protected function validateEffectiveTime(int $timestamp, int $effectiveTime)
    {
        if (($time = time()) < $timestamp || ($time - $timestamp) > $effectiveTime) {
            throw new InvalidSignatureException();
        }
    }

    protected function validateRepeatRequest(string $signature, array $params, int $effectiveTime)
    {
        $cacheSignature = Cache::get($signature);
        if ($cacheSignature) {
            throw new InvalidSignatureException();
        }

        Cache::put($signature, $params, $effectiveTime);
    }
}
