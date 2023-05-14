<?php

declare(strict_types=1);

namespace App\Support\Sse;

class CloseServerSentEventException extends \RuntimeException
{
    public function __construct(public ?ServerSentEvent $serverSentEvent = null, $code = 0, \Throwable $previous = null)
    {
        parent::__construct((string) $serverSentEvent, $code, $previous);
    }
}
