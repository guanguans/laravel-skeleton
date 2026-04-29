<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */
/** @noinspection PhpMethodParametersCountMismatchInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Mixin;

use App\Support\Attribute\Mixin;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\ScheduleListCommand;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use Lorisleiva\CronTranslator\CronTranslator;
use function Illuminate\Filesystem\join_paths;

/**
 * @mixin \Illuminate\Console\Scheduling\Event
 *
 * @method string getClosureLocation(CallbackEvent $event)
 */
#[Mixin(Event::class)]
final class SchedulingEventMixin
{
    /**
     * @noinspection ForgottenDebugOutputInspection
     * @noinspection PhpExpressionResultUnusedInspection
     */
    public function ddExpression(): \Closure
    {
        return function (?string $locale = null, bool $timeFormat24hours = false): never {
            /** @noinspection UnusedFunctionResultInspection */
            $this->dumpExpression($locale, $timeFormat24hours);

            exit(1);
        };
    }

    /**
     * @noinspection ForgottenDebugOutputInspection
     * @noinspection DebugFunctionUsageInspection
     */
    public function dumpExpression(): \Closure
    {
        return function (?string $locale = null, bool $timeFormat24hours = false): Event {
            dump($this->expression.': '.CronTranslator::translate(
                $this->expression,
                match ($locale ??= config()->string('app.locale', 'en')) {
                    'zh_CN' => 'zh',
                    'zh_TW' => 'zh-TW',
                    default => $locale,
                },
                $timeFormat24hours
            ));

            return $this;
        };
    }

    public function dailyAppendOutputTo(): \Closure
    {
        return fn (?string $directory = null, ?string $filename = null): Event => $this->userAppendOutputTo(
            $directory,
            $filename,
            \sprintf('daily-%s', Date::now()->format('Y-m-d'))
        );
    }

    public function weeklyAppendOutputTo(): \Closure
    {
        return fn (?string $directory = null, ?string $filename = null): Event => $this->userAppendOutputTo(
            $directory,
            $filename,
            \sprintf('weekly-%s', Date::now()->format('Y-W'))
        );
    }

    public function monthlyAppendOutputTo(): \Closure
    {
        return fn (?string $directory = null, ?string $filename = null): Event => $this->userAppendOutputTo(
            $directory,
            $filename,
            \sprintf('monthly-%s', Date::now()->format('Y-m'))
        );
    }

    public function quarterlyAppendOutputTo(): \Closure
    {
        return fn (?string $directory = null, ?string $filename = null): Event => $this->userAppendOutputTo(
            $directory,
            $filename,
            \sprintf('quarterly-%s-%s', Date::now()->format('Y'), now()->quarter)
        );
    }

    public function yearlyAppendOutputTo(): \Closure
    {
        return fn (?string $directory = null, ?string $filename = null): Event => $this->userAppendOutputTo(
            $directory,
            $filename,
            \sprintf('yearly-%s', Date::now()->format('Y'))
        );
    }

    /**
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    public function userAppendOutputTo(): \Closure
    {
        return function (?string $directory = null, ?string $filename = null, ?string $suffix = null): Event {
            /** @see \Illuminate\Console\Scheduling\ScheduleListCommand::displayJson() */
            $filename ??= (function (): string {
                /**
                 * @see \Illuminate\Console\Scheduling\Schedule::command()
                 * @see \Illuminate\Console\Scheduling\Schedule::exec()
                 */
                if ($this->command) {
                    return str($this->command)
                        ->pipe(Event::normalizeCommand(...))
                        ->replaceFirst('php artisan', 'artisan')
                        ->toString();
                }

                /**
                 * @see \Illuminate\Console\Scheduling\Schedule::call()
                 * @see \Illuminate\Console\Scheduling\Schedule::job()
                 */
                $command = Event::normalizeCommand($this->getSummaryForDisplay());

                if ($this instanceof CallbackEvent && \in_array($command, ['Closure', 'Callback'], true)) {
                    $callbackEvent = $this;
                    $command = 'Closure at: '.(fn () => $this->getClosureLocation($callbackEvent))->call(
                        tap(resolve(ScheduleListCommand::class))->setLaravel(app())
                    );
                }

                return $command;
            })();

            $filename = str($filename)
                // ->dump()
                ->replace(
                    ['<', '>', '/', '\\', '"', '|', ':', '?', '*', ' ', "\n", "\r", "\t", "\v"],
                    '-'
                )
                ->replaceMatches('/-{3,}/', '--')
                ->remove("'")
                ->take(200)
                ->trim('.-');

            $location = join_paths(
                $directory ?? join_paths(storage_path('logs'), 'schedules', $filename),
                str($filename)
                    ->when($suffix, static fn (Stringable $filename): Stringable => $filename->finish("-$suffix"))
                    ->finish('.log')
            );
            // dump($location);

            return $this
                ->before(static fn () => Log::build(['path' => $location] + config()->array('logging.channels.single'))->info('>>>>>>>>'))
                ->appendOutputTo($location);
        };
    }
}
