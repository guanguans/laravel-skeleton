<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Console\Commands;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use function Illuminate\Filesystem\join_paths;

final class OpcacheUrlCommand extends Command
{
    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    #[\Override]
    protected $signature = 'opcache:url {route=compile} {--force}';

    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    #[\Override]
    protected $description = 'Show OPCache URL';

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(): void
    {
        $preventStrayRequests = Http::preventingStrayRequests();
        Http::preventStrayRequests();
        $this->sendRequest($this->argument('route'), ['force' => $this->option('force')]);
        Http::preventStrayRequests($preventStrayRequests);
    }

    /**
     * @see https://github.com/appstract/laravel-opcache/blob/master/src/CreatesRequest.php
     *
     * @param array<string, mixed> $parameters
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function sendRequest(string $url, array $parameters = []): PromiseInterface|Response
    {
        $baseUri = join_paths(
            rtrim((string) config('opcache.url', config('app.url')), '/'),
            trim((string) config('opcache.prefix', 'opcache-api'), '/'),
            ltrim($url, '/')
        );

        Http::fake(["$baseUri?key=*" => Http::response()]);

        return Http::withHeaders(config('opcache.headers', []))
            ->withOptions([RequestOptions::VERIFY => config('opcache.verify', false)])
            ->beforeSending(fn (Request $request): null => $this->newLine()->line($request->url()))
            ->get($baseUri, ['key' => Crypt::encrypt('opcache'), ...$parameters]);
    }

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function rules(): array
    {
        return [
            'route' => 'in:clear,compile,config,status',
            'force' => 'boolean',
        ];
    }
}
