<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Listeners;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;

class ShareLogContextSubscriber
{
    public function subscribe(Dispatcher $dispatcher): array
    {
        return [
            RouteMatched::class => static function (RouteMatched $event): void {
                Log::shareContext([
                    'action' => $event->route?->getActionName(),
                    'memory_peak_usage' => Number::fileSize(memory_get_peak_usage(), 2),
                ]);
            },
            CommandStarting::class => static function (CommandStarting $event): void {
                Log::shareContext([
                    'command' => $event->command ?? $event->input->getArguments()['command'] ?? 'default',
                    'memory_peak_usage' => Number::fileSize(memory_get_peak_usage(), 2),
                ]);
            },
            ScheduledTaskStarting::class => static function (ScheduledTaskStarting $event): void {
                Log::shareContext([
                    'command' => ($event->task->command ?: $event->task->description) ?: 'Closure',
                    'memory_peak_usage' => Number::fileSize(memory_get_peak_usage(), 2),
                ]);
            },
            RequestHandled::class => $memoryWarningListener = static function (): void {
                $memoryPeakUsage = memory_get_peak_usage();
                $memoryLimit = ini_parse_quantity(\ini_get('memory_limit'));
                if ($memoryPeakUsage > $memoryLimit * 0.6) {
                    Log::warning('Memory usage peak than 60% of memory limit', [
                        'memory_peak_usage' => Number::fileSize($memoryPeakUsage, 2),
                        'memory_limit' => Number::fileSize($memoryLimit, 2),
                    ]);
                }
            },
            CommandFinished::class => $memoryWarningListener,
        ];
    }
}
