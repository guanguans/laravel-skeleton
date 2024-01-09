<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Appstract\Opcache\CreatesRequest;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class OpcacheUrlCommand extends Command
{
    use CreatesRequest;

    protected $signature = 'opcache:url {route=compile} {--force}';

    protected $description = 'Show OPCache URL';

    /**
     * @noinspection PhpMemberCanBePulledUpInspection
     */
    public function handle(): void
    {
        $this->sendRequest($this->argument('route'), ['force' => $this->option('force') ?? false]);
    }

    public function sendRequest(string $url, array $parameters = []): PromiseInterface|Response
    {
        return Http::withHeaders(config('opcache.headers'))
            ->withOptions(['verify' => config('opcache.verify')])
            ->beforeSending(function (Request $request): never {
                $this->line($request->url());

                exit(1);
            })
            ->get(
                rtrim(config('opcache.url'), '/').'/'.trim(config('opcache.prefix'), '/').'/'.ltrim($url, '/'),
                ['key' => Crypt::encrypt('opcache'), ...$parameters]
            );
    }

    protected function rules(): array
    {
        return [
            'route' => 'in:clear,compile,config,status',
            'force' => 'boolean',
        ];
    }
}
