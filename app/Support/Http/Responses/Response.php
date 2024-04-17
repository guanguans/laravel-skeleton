<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Http\Responses;

use App\Support\Http\Support\Collection;
use App\Support\Http\Support\XML;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface;

class Response extends GuzzleResponse implements \Stringable
{
    public function __toString(): string
    {
        return $this->getBodyContents();
    }

    public function getBodyContents(): string
    {
        $this->getBody()->rewind();
        $contents = $this->getBody()->getContents();
        $this->getBody()->rewind();

        return $contents;
    }

    public static function buildFromPsrResponse(ResponseInterface $response): self
    {
        return new static(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function toArray(): array
    {
        $content = $this->getBodyContents();

        if (false !== stripos($this->getHeaderLine('Content-Type'), 'xml') || 0 === stripos($content, '<xml')) {
            return XML::parse($content);
        }

        $array = json_decode($this->getBodyContents(), true);

        if (JSON_ERROR_NONE === json_last_error()) {
            return (array) $array;
        }

        return [];
    }

    public function toCollection(): Collection
    {
        return new Collection($this->toArray());
    }

    public function toObject(): object
    {
        return json_decode($this->getBodyContents());
    }
}
