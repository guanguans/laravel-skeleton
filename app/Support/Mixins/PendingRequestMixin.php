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

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
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
#[Mixin(PendingRequest::class)]
class PendingRequestMixin
{
    public function withLogger(): \Closure
    {
        return function (
            null|LoggerInterface|string $logger = null,
            ?MessageFormatter $formatter = null,
            string $logLevel = 'info'
        ): PendingRequest {
            if (!$logger instanceof LoggerInterface) {
                $logger = Log::channel($logger);
            }

            if (!$logger instanceof Logger) {
                $logger = new Logger($logger, app(Dispatcher::class));
            }

            return $this->withMiddleware(
                Middleware::log($logger, $formatter ?: new MessageFormatter(MessageFormatter::DEBUG), $logLevel)
            );
        };
    }
}
