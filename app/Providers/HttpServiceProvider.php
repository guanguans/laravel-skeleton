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

use App\Support\Contracts\ShouldRegisterContract;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class HttpServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        // JsonResource::wrap('list');
        JsonResource::withoutWrapping();
        ResourceCollection::withoutWrapping();
        Http::globalOptions([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);
        Http::globalMiddleware(
            Middleware::log(Log::channel('single'), new MessageFormatter(MessageFormatter::DEBUG))
        );
    }

    public function shouldRegister(): bool
    {
        return true;
    }
}
