<?php

namespace App\Http\Middleware;

use App\Support\Signer\HmacSigner;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class VerifySignature
{
    /**
     * @var \App\Support\Signer\HmacSigner
     */
    protected $signer;

    public function __construct(HmacSigner $signer)
    {
        $this->signer = $signer;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->headers->get('signature');
        $timestamp = $request->headers->get('timestamp');
        $nonce = $request->headers->get('nonce');

        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            $params = $request->query();
        } else {
            $params = $request->post();
        }

        $params['timestamp'] = $timestamp;
        $params['nonce'] = $nonce;

        if (!$this->signer->validate($signature, $params)) {
            throw new InvalidSignatureException();
        }

        return $next($request);
    }
}
