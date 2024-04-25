<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

abstract class AbortIf
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return tap($next($request), function (): void {
            abort_if($this->condition(), $this->code(), $this->message(), $this->headers());
        });
    }

    abstract protected function condition(): bool;

    abstract protected function code(): int;

    protected function message()
    {
        return '';
    }

    protected function headers(): array
    {
        return [];
    }
}
