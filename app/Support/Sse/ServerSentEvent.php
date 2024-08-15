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
    final public const HEADERS = [
        'Content-Type' => 'text/event-stream',
        'Connection' => 'keep-alive',
        'Cache-Control' => 'no-cache, no-store, must-revalidate, pre-check=0, post-check=0',
        'X-Accel-Buffering' => 'no',
    ];

    /** @var null|callable */
    private $tapper;

    private bool $headersSent = false;

    private array $beforeCallbacks = [];

    private array $afterCallbacks = [];

    public function __construct(
        ?callable $tapper = null,
        private null|string|\Stringable $event = null,
        private null|array|string|\Stringable $data = null,
        private null|string|\Stringable $id = null,
        private null|string|\Stringable $comment = null,
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

    public function __invoke(): void
    {
        // register_tick_function(function () {
        //     if (connection_aborted()) {
        //         throw new CloseServerSentEventException;
        //     }
        // });
        //
        // declare(ticks=1) {
        //     // Event loop code.
        // }

        // Event loop.
        for (;;) {
            try {
                // Echo server sent event.
                $this->send();
            } catch (CloseServerSentEventException $e) {
                // $e->serverSentEvent?->sendContent();
                $e->serverSentEvent?->send();

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
                \Illuminate\Support\Sleep::sleep($this->sleep);
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

    public function isHeadersSent(): bool
    {
        return $this->headersSent;
    }

    public function getEvent(): null|string|\Stringable
    {
        return $this->event;
    }

    public function setEvent(null|string|\Stringable $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getData(): null|array|string|\Stringable
    {
        return $this->data;
    }

    public function setData(null|array|string|\Stringable $data, int $options = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): self
    {
        if (\is_array($data)) {
            $data = json_encode($data, $options);
        }

        $this->data = $data;

        return $this;
    }

    public function getId(): null|string|\Stringable
    {
        return $this->id;
    }

    public function setId(null|string|\Stringable $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getComment(): null|string|\Stringable
    {
        return $this->comment;
    }

    public function setComment(null|string|\Stringable $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRetry(): ?int
    {
        return $this->retry;
    }

    public function setRetry(?int $retry): self
    {
        $this->retry = $retry;

        return $this;
    }

    public function getSleep(): int
    {
        return $this->sleep;
    }

    public function setSleep(int $sleep): self
    {
        $this->sleep = $sleep;

        return $this;
    }

    public function before(\Closure $callback): self
    {
        $this->beforeCallbacks[] = $callback;

        return $this;
    }

    public function after(\Closure $callback): self
    {
        return $this->then($callback);
    }

    public function then(\Closure $callback): self
    {
        $this->afterCallbacks[] = $callback;

        return $this;
    }

    public function send(): self
    {
        $this->callBeforeCallbacks();
        $this->sendHeaders();
        $this->sendContent();
        $this->callAfterCallbacks();

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
        echo ($this->tapper)($this);

        return $this;
    }

    public function callBeforeCallbacks(): void
    {
        foreach ($this->beforeCallbacks as $callback) {
            $callback($this);
        }
    }

    public function callAfterCallbacks(): void
    {
        foreach ($this->afterCallbacks as $callback) {
            $callback($this);
        }
    }
}
