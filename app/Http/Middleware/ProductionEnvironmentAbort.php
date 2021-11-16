<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ProductionEnvironmentAbort extends AbortIf
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $code
     * @param  string  $message
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $code = 404, $message = '', $headers = [])
    {
        return parent::handle($request, $next, App::isProduction(), $code, $message, $headers);
    }
}
