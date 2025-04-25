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

namespace App\Listeners;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Context;

class LogContextSubscriber
{
    public function subscribe(Dispatcher $dispatcher): array
    {
        return [
            RouteMatched::class => static function (RouteMatched $event): void {
                Context::add([
                    'action' => $event->route?->getActionName(),
                ]);
            },
            CommandStarting::class => static function (CommandStarting $event): void {
                Context::add([
                    'command' => $event->command ?? $event->input->getArguments()['command'] ?? 'default',
                ]);
            },
            ScheduledTaskStarting::class => static function (ScheduledTaskStarting $event): void {
                Context::add([
                    'command' => ($event->task->command ?: $event->task->description) ?: 'Closure',
                ]);
            },
        ];
    }
}
