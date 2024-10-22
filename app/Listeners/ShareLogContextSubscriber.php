<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Log;

class ShareLogContextSubscriber
{
    public function subscribe(Dispatcher $dispatcher): array
    {
        return [
            RouteMatched::class => static function (RouteMatched $event): void {
                Log::shareContext([
                    'action' => $event->route?->getActionName(),
                ]);
            },
            CommandStarting::class => static function (CommandStarting $event): void {
                Log::shareContext([
                    'command' => $event->command ?? $event->input->getArguments()['command'] ?? 'default',
                ]);
            },
            ScheduledTaskStarting::class => static function (ScheduledTaskStarting $event): void {
                Log::shareContext([
                    'command' => ($event->task->command ?: $event->task->description) ?: 'Closure',
                ]);
            },
            RequestHandled::class => static function (RequestHandled $event): void {
                Log::shareContext([
                    'memory_peak_usage' => memory_get_peak_usage(),
                    'memory_usage' => memory_get_usage(),
                ]);
            },
            CommandFinished::class => static function (CommandFinished $event): void {
                Log::shareContext([
                    'memory_peak_usage' => memory_get_peak_usage(),
                    'memory_usage' => memory_get_usage(),
                ]);
            },
        ];
    }
}
