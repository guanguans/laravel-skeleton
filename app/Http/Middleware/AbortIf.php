<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request;

abstract class AbortIf
{
    public function handle(Request $request, \Closure $next): mixed
    {
        return tap($next($request), function (): void {
            abort_if($this->condition(), $this->code(), $this->message(), $this->headers());
        });
    }

    abstract protected function condition(): bool;

    abstract protected function code(): int;

    protected function message(): string
    {
        return '';
    }

    protected function headers(): array
    {
        return [];
    }
}
