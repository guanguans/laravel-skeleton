<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Sse;

class CloseServerSentEventException extends \RuntimeException
{
    public function __construct(public ?ServerSentEvent $serverSentEvent = null, $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct((string) $serverSentEvent, $code, $previous);
    }
}
