<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */
/** @noinspection PhpMethodParametersCountMismatchInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;

/**
 * @mixin \Illuminate\Console\Scheduling\Event
 *
 * @property $channels
 */
#[Mixin(Event::class)]
class SchedulingEventMixin
{
    public function userAppendOutputToDaily(): callable
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, \sprintf('daily-%s', Carbon::now()->format('Y-m-d')), $dirname);
    }

    public function userAppendOutputToWeekly(): callable
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, \sprintf('weekly-%s', Carbon::now()->format('Y-W')), $dirname);
    }

    public function userAppendOutputToMonthly(): callable
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, \sprintf('monthly-%s', Carbon::now()->format('Y-m')), $dirname);
    }

    public function userAppendOutputToQuarterly(): callable
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo(
            $filename,
            \sprintf('quarterly-%s-%s', Carbon::now()->format('Y'), now()->quarter),
            $dirname
        );
    }

    public function userAppendOutputToYearly(): callable
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, \sprintf('yearly-%s', Carbon::now()->format('Y')), $dirname);
    }

    public function userAppendOutputTo(): callable
    {
        return function (?string $filename = null, ?string $suffix = null, ?string $dirname = null): Event {
            $outputPath = value(
                function (?string $filename, ?string $suffix, ?string $dirname): string {
                    $filename = value(
                        function (?string $filename): string {
                            if ($filename) {
                                return $filename;
                            }

                            // artisan
                            if (str($this->command)->contains("'artisan'")) {
                                $commands = explode(' ', $this->command);

                                return $commands[array_search("'artisan'", $commands, true) + 1];
                            }

                            throw_if(empty($this->description), \LogicException::class, "Please incoming the \$filename parameter, Or use the 'name' method before 'userAppendOutputTo'.");

                            // exec|call|job
                            return $this->description;
                        },
                        $filename
                    );

                    $normalizedFilename = str($filename)->replace([\DIRECTORY_SEPARATOR, '\\', ' '], ['-', '-', '-']);

                    return (
                        $dirname
                            ? str($dirname)
                            : str(storage_path('logs'))
                                ->finish(\DIRECTORY_SEPARATOR)
                                ->append('schedules')
                                ->finish(\DIRECTORY_SEPARATOR)
                                ->append($normalizedFilename)
                    )
                        ->finish(\DIRECTORY_SEPARATOR)
                        ->append($normalizedFilename)
                        ->when(
                            $suffix,
                            static fn (
                                Stringable $stringable,
                                string $suffix
                            ) => $stringable->finish('-')->finish($suffix)
                        )
                        ->append('.log')
                        ->toString();
                },
                $filename,
                $suffix,
                $dirname,
            );

            // dump($outputPath);

            return $this
                ->before(function () use ($outputPath): void {
                    $singleLogPath = config('logging.channels.single.path');
                    $unsetSingleChannelHandler = function (): void {
                        unset($this->channels['single']);
                    };

                    config()->set('logging.channels.single.path', $outputPath);
                    $unsetSingleChannelHandler->call(app('log'));

                    Log::channel('single')->info('>>>>>>>>');

                    config()->set('logging.channels.single.path', $singleLogPath);
                    $unsetSingleChannelHandler->call(app('log'));
                })
                ->appendOutputTo($outputPath);
        };
    }
}
