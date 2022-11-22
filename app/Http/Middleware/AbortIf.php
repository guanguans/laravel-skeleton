<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

abstract class AbortIf
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return tap($next($request), function () {
            abort_if($this->condition(), $this->code(), $this->message(), $this->headers());
        });
    }

    abstract protected function condition(): bool;

    protected function code()
    {
        return 404;
    }

    protected function message()
    {
        return '';
    }

    protected function headers(): array
    {
        return [];
    }
}
