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

namespace App\Support\Sse;

class CloseServerSentEventException extends \RuntimeException
{
    public function __construct(
        public ?ServerSentEvent $serverSentEvent = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct((string) $serverSentEvent, $code, $previous);
    }
}
