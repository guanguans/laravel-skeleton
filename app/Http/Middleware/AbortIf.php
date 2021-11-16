<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AbortIf
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param $condition
     * @param  int  $code
     * @param  string  $message
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $condition, $code = 404, $message = '', $headers = [])
    {
        abort_if($condition, $code, $message, $headers);

        return $next($request);
    }
}
