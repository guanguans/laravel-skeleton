<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Events\Dispatcher;
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
        ];
    }
}
