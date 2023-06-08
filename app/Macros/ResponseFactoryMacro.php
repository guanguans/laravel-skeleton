<?php

declare(strict_types=1);

namespace App\Macros;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @see https://github.com/TitasGailius/laravel-stream-remote
 *
 * @mixin \Illuminate\Routing\ResponseFactory
 */
class ResponseFactoryMacro
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
            $client ??= new Client();

            return $this->streamDownload(function () use ($client, $url, $chunk): void {
                $body = $client->get($url, ['stream' => true])->getBody();
                while (! $body->eof()) {
                    echo $body->read($chunk);
                }
            }, $name, $headers, $disposition);
        };
    }
}
