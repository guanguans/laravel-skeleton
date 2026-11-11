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

namespace App\Listeners;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\ScheduleListCommand;
use Illuminate\Foundation\Events\Terminating;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Number;

/**
 * @method string getClosureLocation(CallbackEvent $event)
 */
final class ContextSubscriber
{
    /**
     * @see \Symfony\Component\Console\DataCollector\CommandDataCollector
     * @see \Symfony\Component\Console\Debug\CliRequest
     * @see \Symfony\Component\Console\Command\TraceableCommand
     *
     * @api
     *
     * @return array<class-string, callable(object $event): void>
     */
    public function subscribe(): array
    {
        return [
            CommandStarting::class => static function (CommandStarting $commandStarting): void {
                Context::add('Command', Event::normalizeCommand((string) $commandStarting->input));
            },
            RouteMatched::class => static function (RouteMatched $routeMatched): void {
                Context::add('Controller Action', $routeMatched->route->getActionName());
            },
            ScheduledTaskStarting::class => static function (ScheduledTaskStarting $scheduledTaskStarting): void {
                Context::add('Command', self::resolveCommand($scheduledTaskStarting->task));
                Context::add('In Schedule', true);
            },
            Terminating::class => static function (): void {
                Context::add('Memory', Number::fileSize(memory_get_peak_usage(true)));

                /**
                 * @see \Illuminate\Foundation\Console\Kernel::commandStartedAt()
                 * @see \Illuminate\Foundation\Console\Kernel::whenCommandLifecycleIsLongerThan()
                 * @see \Illuminate\Foundation\Http\Kernel::requestStartedAt()
                 * @see \Illuminate\Foundation\Http\Kernel::whenRequestLifecycleIsLongerThan()
                 */
                Context::add('Duration', blank(
                    $startTime = \defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT')
                ) ? 'Unknown' : humans_milliseconds((microtime(true) - (float) $startTime) * 1000));
            },
        ];
    }

    /**
     * @see \Illuminate\Console\Scheduling\ScheduleListCommand::displayJson()
     */
    private static function resolveCommand(Event $schedulingEvent): string
    {
        /**
         * @see \Illuminate\Console\Scheduling\Schedule::command()
         * @see \Illuminate\Console\Scheduling\Schedule::exec()
         */
        if ($schedulingEvent->command) {
            return Event::normalizeCommand($schedulingEvent->command);
        }

        /**
         * @see \Illuminate\Console\Scheduling\Schedule::call()
         * @see \Illuminate\Console\Scheduling\Schedule::job()
         */
        $command = Event::normalizeCommand($schedulingEvent->getSummaryForDisplay());

        if (!$schedulingEvent instanceof CallbackEvent || !\in_array($command, ['Callback', 'Closure'], true)) {
            return $command;
        }

        return 'Closure at: '.(fn () => $this->getClosureLocation($schedulingEvent))->call(
            tap(resolve(ScheduleListCommand::class))->setLaravel(app())
        );
    }
}
