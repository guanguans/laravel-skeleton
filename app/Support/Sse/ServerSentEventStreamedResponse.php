<?php

namespace App\Support\Sse;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ```php
 * Route::get('stream', static function (): App\Support\Sse\ServerSentEventStreamedResponse {
 *     return new App\Support\Sse\ServerSentEventStreamedResponse(
 *         new App\Support\Sse\ServerSentEvent(static function (App\Support\Sse\ServerSentEvent $serverSentEvent): void {
 *             $serverSentEvent
 *                 ->setEvent($event = (static function (): string {
 *                     $events = ['', 'message', 'notice'];
 *                     $index = array_rand($events, 1);
 *
 *                     return $events[$index];
 *                 })())
 *                 ->setData(['event' => $event, 'time' => time()])
 *                 ->setId(uniqid('', true))
 *                 ->setComment('comment')
 *                 ->setRetry(3000)
 *                 ->setSleep(3);
 *         }),
 *         App\Support\Sse\ServerSentEventStreamedResponse::HTTP_OK,
 *         ['Access-Control-Allow-Origin' => '*']
 *     );
 * });
 * ```
 *
 * ```guzzle
 * (new GuzzleHttp\Client())->get(
 *     'https://strangecat-api.test/stream',
 *     [
 *         'curl' => [
 *             CURLOPT_WRITEFUNCTION => static function (object $ch, string $data) {
 *                 dump($data);
 *
 *                 return strlen($data);
 *             },
 *         ],
 *     ]
 * );
 * ```
 *
 * ```js
 * const source = new EventSource('https://strangecat-api.test/stream');
 * source.onopen = event => console.log('onopen', event);
 * source.onerror = event => console.log('onerror', event);
 * source.onmessage = event => {
 *     console.log(event.data);
 *     // source.close(); // Close event stream.
 * };
 * source.addEventListener('notice', event => {
 *     console.log(event.data);
 *     // source.close(); // Close event stream.
 * });
 * ```
 *
 * @see https://developer.mozilla.org/zh-CN/docs/Web/API/EventSource
 * @see https://developer.mozilla.org/zh-CN/docs/Web/API/Server-sent_events/Using_server-sent_events
 * @see https://github.com/mdn/dom-examples/tree/main/server-sent-events
 * @see https://github.com/hhxsv5/php-sse
 * @see https://github.com/sarfraznawaz2005/laravel-sse
 * @see https://github.com/qruto/laravel-wave
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

        return parent::setCallback($callback);
    }

    public function sendHeaders(): static
    {
        foreach (self::HEADERS as $name => $header) {
            $this->headers->set($name, $header);
        }

        return parent::sendHeaders();
    }
}
