<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
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
final class ResponseFactoryMixin
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
     * ```.
     *
     * @noinspection SensitiveParameterInspection
     * @noinspection PhpTooManyParametersInspection
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

                while (!$body->eof()) {
                    echo $body->read($chunk);
                }
            }, $name, $headers, $disposition);
        };
    }
}
