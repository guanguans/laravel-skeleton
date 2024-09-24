<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Macros;

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * @mixin \Illuminate\Http\Client\PendingRequest
 *
 * @see https://github.com/TheDragonCode/laravel-http-macros
 */
class PendingRequestMacro
{
    public function withLogger(): \Closure
    {
        return function (
            null|LoggerInterface|string $logger = null,
            ?MessageFormatter $formatter = null,
            string $logLevel = 'info'
        ): PendingRequest {
            if (! $logger instanceof LoggerInterface) {
                $logger = Log::channel($logger);
            }

            if (! $logger instanceof Logger) {
                $logger = new Logger($logger, app(Dispatcher::class));
            }

            return $this->withMiddleware(
                Middleware::log($logger, $formatter ?: new MessageFormatter(MessageFormatter::DEBUG), $logLevel)
            );
        };
    }
}
