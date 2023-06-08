<?php

declare(strict_types=1);

namespace App\Support\Sse;

/**
 * ```php
 * Route::get('stream', static function (): Symfony\Component\HttpFoundation\StreamedResponse {
 *     return new Symfony\Component\HttpFoundation\StreamedResponse(
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
 *         Symfony\Component\HttpFoundation\Response::HTTP_OK,
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
class ServerSentEvent implements \Stringable
{
    /**
     * @var array<string, string>
     */
    public const HEADERS = [
        'Content-Type' => 'text/event-stream',
        'Connection' => 'keep-alive',
        'Cache-Control' => 'no-cache, no-store, must-revalidate, pre-check=0, post-check=0',
        'X-Accel-Buffering' => 'no',
    ];

    /**
     * @var null|callable
     */
    private $tapper;

    private bool $headersSent = false;

    public function __construct(
        ?callable $tapper = null,
        private string|\Stringable|null $event = null,
        private string|array|\Stringable|null $data = null,
        private string|\Stringable|null $id = null,
        private string|\Stringable|null $comment = null,
        private ?int $retry = 3000,
        private int $sleep = 3,
    ) {
        $this->setTapper($tapper);
        $this->setData($data);
    }

    public function __toString(): string
    {
        $event = [];
        if (null !== $this->event) {
            $event[] = "event: $this->event";
        }
        if (null !== $this->data) {
            $event[] = "data: $this->data";
        }
        if (null !== $this->id) {
            $event[] = "id: $this->id";
        }
        if (null !== $this->comment) {
            $event[] = ": $this->comment";
        }
        if (null !== $this->retry) {
            $event[] = "retry: $this->retry";
        }

        return implode(PHP_EOL, $event).PHP_EOL.PHP_EOL;
    }

    public function __invoke()
    {
        // Event loop.
        for (; ;) {
            try {
                // Echo server sent event.
                $this->send();
            } catch (CloseServerSentEventException $e) {
                // $e->serverSentEvent?->send();
                $e->serverSentEvent?->sendContent();

                return;
            } finally {
                // Flush the output buffer and send echoed messages to the browser.
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

                // Break the loop if the client aborted the connection.
                if (connection_aborted()) {
                    return;
                }

                // Sleep seconds before running the loop again.
                sleep($this->sleep);
            }
        }
    }

    public function setTapper(?callable $tapper): self
    {
        $this->tapper = static function (self $serverSentEvent) use ($tapper): self {
            if (\is_callable($tapper)) {
                $tapper($serverSentEvent);
            }

            return $serverSentEvent;
        };

        return $this;
    }

    public function setEvent(\Stringable|string|null $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function setData(\Stringable|array|string|null $data, $options = JSON_THROW_ON_ERROR): self
    {
        if (\is_array($data)) {
            $data = json_encode($data, $options);
        }

        $this->data = $data;

        return $this;
    }

    public function setId(\Stringable|string|null $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setComment(\Stringable|string|null $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function setRetry(?int $retry): self
    {
        $this->retry = $retry;

        return $this;
    }

    public function setSleep(int $sleep): self
    {
        $this->sleep = $sleep;

        return $this;
    }

    public function sendHeaders(): self
    {
        // headers have already been sent by the developer
        if ($this->headersSent || headers_sent()) {
            return $this;
        }

        $this->headersSent = true;

        // headers
        foreach (self::HEADERS as $name => $value) {
            header($name.': '.$value, 0 === strcasecmp($name, 'Content-Type'));
        }

        return $this;
    }

    public function sendContent(): self
    {
        echo($this->tapper)($this);

        return $this;
    }

    public function send(): self
    {
        $this->sendHeaders();
        $this->sendContent();

        return $this;
    }
}
