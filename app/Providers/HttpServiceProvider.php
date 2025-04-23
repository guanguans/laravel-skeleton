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

namespace App\Providers;

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;
use Symfony\Component\HttpFoundation\JsonResponse;

class HttpServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, static function (): void {
            JsonResource::withoutWrapping();
            ResourceCollection::withoutWrapping();

            Http::globalOptions([
                'connect_timeout' => 10,
                'timeout' => 30,
            ]);
            Http::globalMiddleware(
                Middleware::log(Log::channel(), new MessageFormatter(MessageFormatter::DEBUG))
            );

            Event::listen(RequestHandled::class, static function (RequestHandled $event): void {
                if ($event->response instanceof JsonResponse) {
                    $event->response->setEncodingOptions(
                        $event->response->getEncodingOptions() | \JSON_UNESCAPED_UNICODE
                    );
                }
            });

            if (RequestFacade::is('api/*')) {
                app(Request::class)->headers->set('Accept', 'application/json');
            }
        });
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {
            JsonResource::wrap('list');
        });
    }
}
