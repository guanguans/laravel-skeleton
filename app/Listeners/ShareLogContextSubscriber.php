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
            RouteMatched::class => static function (RouteMatched $routeMatched): void {
                Log::shareContext([
                    'action' => $routeMatched->route?->getActionName(),
                ]);
            },
            CommandStarting::class => static function (CommandStarting $commandStarting): void {
                Log::shareContext([
                    'command' => $commandStarting->command,
                ]);
            },
            ScheduledTaskStarting::class => static function (ScheduledTaskStarting $scheduledTaskStarting): void {
                Log::shareContext([
                    'command' => $scheduledTaskStarting->task->command ?: $scheduledTaskStarting->task->description,
                ]);
            },
        ];
    }
}
