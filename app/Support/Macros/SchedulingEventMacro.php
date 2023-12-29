<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */
/** @noinspection PhpMethodParametersCountMismatchInspection */

declare(strict_types=1);

namespace App\Support\Macros;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;

/**
 * @mixin Event
 *
 * @property $channels
 */
class SchedulingEventMacro
{
    public function userAppendOutputToDaily(): callable
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, 'Y-m-d', $dirname);
    }

    public function userAppendOutputToWeekly(): callable
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, 'Y-W', $dirname);
    }

    public function userAppendOutputToMonthly(): callable
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, 'Y-m', $dirname);
    }

    public function userAppendOutputToQuarterly(): callable
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, sprintf('Y-%s', now()->quarter), $dirname);
    }

    public function userAppendOutputToYearly(): callable
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, 'Y', $dirname);
    }

    public function userAppendOutputTo(): callable
    {
        return function (?string $filename = null, ?string $suffixRule = null, ?string $dirname = null): Event {
            $outputPath = value(
                function (?string $filename, ?string $suffixRule, ?string $dirname): string {
                    $filename = value(
                        function (?string $filename): string {
                            if ($filename) {
                                return $filename;
                            }

                            // artisan
                            if (str($this->command)->contains("'artisan'")) {
                                $commands = (array) explode(' ', $this->command);

                                return $commands[array_search("'artisan'", $commands, true) + 1];
                            }

                            /** @see \Illuminate\Console\Scheduling\CallbackEvent::withoutOverlapping */
                            if ($this->description === null) {
                                throw new \LogicException(
                                    "Please incoming the \$filename parameter, Or use the 'name' method before 'userAppendOutputTo'."
                                );
                            }

                            // exec|call|job
                            return $this->description;
                        },
                        $filename
                    );

                    $normalizedFilename = str($filename)
                        // ->replaceMatches([sprintf('/\%s/', DIRECTORY_SEPARATOR), '/\s+/'], ['-', '-'])
                        ->replace([DIRECTORY_SEPARATOR, '\\', ' '], ['-', '-', '-']);

                    return (
                        $dirname
                            ? str($dirname)
                            : str(storage_path('logs'))
                                ->finish(DIRECTORY_SEPARATOR)
                                ->append('schedules')
                                ->finish(DIRECTORY_SEPARATOR)
                                ->append($normalizedFilename)
                    )
                        ->finish(DIRECTORY_SEPARATOR)
                        ->append($normalizedFilename)
                        ->when(
                            $suffixRule,
                            static fn (
                                Stringable $stringable,
                                string $suffixRule
                            ) => $stringable->finish('-')->finish(date($suffixRule)))
                        ->append('.log')
                        ->toString();
                },
                $filename,
                $suffixRule,
                $dirname,
            );

            // dump($outputPath);

            return $this
                ->before(function () use ($outputPath) {
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
