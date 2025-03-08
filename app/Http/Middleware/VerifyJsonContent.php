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

use App\Exceptions\BadRequestException;
use Illuminate\Http\Request;

class VerifyJsonContent
{
    /**
     * @throws BadRequestException
     */
    public function handle(Request $request, \Closure $next)
    {
        $acceptHeader = $request->header('accept');
        $contentType = 'application/json';

        throw_unless(str_contains($acceptHeader, $contentType), BadRequestException::class, 'Your request must contain [Accept = application/json].');

        $response = $next($request);

        $response->headers->set('Content-Type', $contentType);

        if (!str_contains($acceptHeader, $contentType)) {
            $warnCode = '199'; // https://www.iana.org/assignments/http-warn-codes/http-warn-codes.xhtml
            $warnMessage = 'Missing request header [ accept = '.$contentType.' ] when calling a JSON API.';
            $response->headers->set('Warning', $warnCode.' '.$warnMessage);
        }

        return $response;
    }
}
