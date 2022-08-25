<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerifyCommonParameters
{
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
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Validator::make($request->headers(), $this->rules)->validate();

        return $next($request);
    }
}
