<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Console\Commands;

use Appstract\Opcache\CreatesRequest;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use function Illuminate\Filesystem\join_paths;

final class OpcacheUrlCommand extends Command
{
    // use CreatesRequest;
    protected $signature = 'opcache:url {route=compile} {--force}';
    protected $description = 'Show OPCache URL';

    /**
     * @noinspection PhpMemberCanBePulledUpInspection
     */
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
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function sendRequest(string $url, array $parameters = []): PromiseInterface|Response
    {
        $baseUri = join_paths(
            rtrim(config('opcache.url', config('app.url')), '/'),
            trim(config('opcache.prefix', 'opcache-api'), '/'),
            ltrim($url, '/')
        );

        Http::fake([
            "$baseUri?key=*" => Http::response(),
        ]);

        return Http::withHeaders(config('opcache.headers', []))
            ->withOptions(['verify' => config('opcache.verify', false)])
            ->retry(0)
            ->beforeSending(function (Request $request): void {
                static $isPrinted = false;

                if ($isPrinted) {
                    return;
                }

                $this->line($request->url());
                $isPrinted = true;
            })
            ->get(
                $baseUri,
                ['key' => Crypt::encrypt('opcache'), ...$parameters]
            );
    }

    #[\Override]
    protected function rules(): array
    {
        return [
            'route' => 'in:clear,compile,config,status',
            'force' => 'boolean',
        ];
    }
}
