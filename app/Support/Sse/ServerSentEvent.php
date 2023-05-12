<?php

namespace App\Support\Sse;

class ServerSentEvent implements \Stringable
{
    /**
     * @var callable|null
     */
    private $callback;

    public function __construct(
        ?callable $callback = null,
        private string|array|\Stringable|null $data = null,
        private string|\Stringable|null $id = null,
        private string|\Stringable|null $event = null,
        private ?int $retry = 5000,
        private string|\Stringable|null $comment = null,
        private int $sleep = 3,
    ) {
        $this->callback = $callback;
        $this->setData($data);
    }

    public function setCallback(?callable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @throws \JsonException
     */
    public function setData(\Stringable|array|string|null $data): void
    {
        if (\is_array($data)) {
            $data = json_encode($data, JSON_THROW_ON_ERROR);
        }

        $this->data = $data;
    }

    public function setId(\Stringable|string|null $id): void
    {
        $this->id = $id;
    }

    public function setEvent(\Stringable|string|null $event): void
    {
        $this->event = $event;
    }

    public function setRetry(?int $retry): void
    {
        $this->retry = $retry;
    }

    public function setComment(\Stringable|string|null $comment): void
    {
        $this->comment = $comment;
    }

    public function setSleep(int $sleep): void
    {
        $this->sleep = $sleep;
    }

    public function __toString(): string
    {
        $event = [];
        if ($this->id !== null) {
            $event[] = "id: $this->id";
        }

        if ($this->event !== null) {
            $event[] = "event: $this->event";
        }

        if ($this->data !== null) {
            $event[] = "data: $this->data";
        }

        if ($this->retry !== null) {
            $event[] = "retry: $this->retry";
        }

        if ($this->comment !== null) {
            $event[] = ": $this->comment";
        }

        return implode(PHP_EOL, $event).PHP_EOL.PHP_EOL;
    }

    public function __invoke()
    {
        if ($this->callback) {
            ($this->callback)($this);
        }

        echo $this;

        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();

        // if the connection has been closed by the client we better exit the loop
        if (connection_aborted()) {
            return;
        }

        sleep($this->sleep);
    }
}
