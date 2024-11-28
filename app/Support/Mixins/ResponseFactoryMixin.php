<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use GuzzleHttp\Client;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @see https://github.com/TitasGailius/laravel-stream-remote
 *
 * @mixin \Illuminate\Routing\ResponseFactory
 */
#[Mixin(ResponseFactory::class)]
class ResponseFactoryMixin
{
    /**
     * ```
     * return response()->streamRemoteDownload(
     *     'https://example.com/remote/file.zip',
     *     'archive.zip',
     *     ['Content-Type' => 'application/zip'],
     *     'attachment',
     *     1024
     * );
     * ```
     */
    public function streamRemoteDownload(): \Closure
    {
        return function (
            string $url,
            ?string $name = null,
            array $headers = [],
            ?string $disposition = 'attachment',
            int $chunk = 2048,
            ?Client $client = null
        ): StreamedResponse {
            $client ??= new Client;

            return $this->streamDownload(static function () use ($client, $url, $chunk): void {
                $body = $client->get($url, ['stream' => true])->getBody();
                while (! $body->eof()) {
                    echo $body->read($chunk);
                }
            }, $name, $headers, $disposition);
        };
    }
}
