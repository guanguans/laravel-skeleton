<?php

namespace App\Support\Sse;

class CloseServerSentEventException extends \RuntimeException
{
    public function __construct(public ?ServerSentEvent $serverSentEvent = null)
    {
        parent::__construct((string) $this->serverSentEvent ?: 'Close server sent event.');
    }
}
