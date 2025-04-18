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

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbortIf
{
    /**
     * @noinspection RedundantDocCommentTagInspection
     *
     * @param \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        return tap($next($request), function (): void {
            abort_if($this->when(), $this->code(), $this->message(), $this->headers());
        });
    }

    abstract protected function when(): bool;

    abstract protected function code(): int|Responsable|Response;

    protected function message(): string
    {
        return '';
    }

    protected function headers(): array
    {
        return [];
    }
}
