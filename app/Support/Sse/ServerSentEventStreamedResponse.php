<?php

namespace App\Support\Sse;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ```php
 * Route::get('stream', function () {
 *     return new ServerSentEventStreamedResponse(new ServerSentEvent(function (ServerSentEvent $event) {
 *         $event->setData(json_encode(['message' => 'Hello World'.time()]));
 *     }));
 * });
 * ```
 */
class ServerSentEventStreamedResponse extends StreamedResponse
{
    /**
     * @var array<string, string>
     */
    protected const HEADERS = [
        'Content-Type' => 'text/event-stream',
        'Connection' => 'keep-alive',
        'Cache-Control' => 'no-cache, no-store, must-revalidate, pre-check=0, post-check=0',
        'X-Accel-Buffering' => 'no',
    ];

    public function __construct(ServerSentEvent $serverSentEvent = null, int $status = 200, array $headers = [])
    {
        parent::__construct($serverSentEvent, $status, $headers);
    }

    public function setCallback(callable $callback): static
    {
        if (! $callback instanceof ServerSentEvent) {
            throw new \InvalidArgumentException(sprintf('The callback must be an instance of %s', ServerSentEvent::class));
        }

        return parent::setCallback(function () use ($callback) {
            while (true) {
                $callback();
            }
        });
    }

    public function sendHeaders(): static
    {
        foreach (self::HEADERS as $name => $header) {
            $this->headers->set($name, $header);
        }

        return parent::sendHeaders();
    }
}
