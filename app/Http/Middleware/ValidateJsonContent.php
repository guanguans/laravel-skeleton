<?php

namespace App\Http\Middleware;

use App\Exceptions\BadRequestException;
use Closure;
use Illuminate\Http\Request;

class ValidateJsonContent
{
    /**
     * @throws BadRequestException
     */
    public function handle(Request $request, Closure $next)
    {
        $acceptHeader = $request->header('accept');
        $contentType = 'application/json';

        if (! str_contains($acceptHeader, $contentType)) {
            throw new BadRequestException('Your request must contain [Accept = application/json].');
        }

        $response = $next($request);

        $response->headers->set('Content-Type', $contentType);

        if (! str_contains($acceptHeader, $contentType)) {
            $warnCode = '199'; // https://www.iana.org/assignments/http-warn-codes/http-warn-codes.xhtml
            $warnMessage = 'Missing request header [ accept = ' . $contentType . ' ] when calling a JSON API.';
            $response->headers->set('Warning', $warnCode . ' ' . $warnMessage);
        }

        return $response;
    }
}
