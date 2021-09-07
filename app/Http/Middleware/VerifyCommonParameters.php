<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TiMacDonald\Middleware\HasParameters;

class VerifyCommonParameters
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
     * @param  null|array  $parameters
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, array $parameters = null)
    {
        $this->validateParams(array_key_reduce($this->rules, function ($carry, $rule, $parameter) use ($request) {
            $carry[$parameter] = $request->header($parameter);

            return $carry;
        }, []));

        return $next($request);
    }

    protected function validateParams(array $params)
    {
        $validator = Validator::make($params, $this->rules);
        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }
}
