<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */
/** @noinspection PhpMethodParametersCountMismatchInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use Lorisleiva\CronTranslator\CronTranslator;

/**
 * @mixin \Illuminate\Console\Scheduling\Event
 */
#[Mixin(Event::class)]
final class SchedulingEventMixin
{
    /**
     * @noinspection ForgottenDebugOutputInspection
     */
    public function ddHumanlyExpression(): \Closure
    {
        return function (string $locale = 'en', bool $timeFormat24hours = false): void {
            dd(CronTranslator::translate($this->expression, $locale, $timeFormat24hours));
        };
    }

    /**
     * @noinspection ForgottenDebugOutputInspection
     * @noinspection DebugFunctionUsageInspection
     */
    public function dumpHumanlyExpression(): \Closure
    {
        return function (string $locale = 'en', bool $timeFormat24hours = false): Event {
            dump(CronTranslator::translate($this->expression, $locale, $timeFormat24hours));

            return $this;
        };
    }

    /**
     * @noinspection ForgottenDebugOutputInspection
     */
    public function ddExpression(): \Closure
    {
        return function (): void {
            dd($this->expression);
        };
    }

    /**
     * @noinspection ForgottenDebugOutputInspection
     * @noinspection DebugFunctionUsageInspection
     */
    public function dumpExpression(): \Closure
    {
        return function (): Event {
            dump($this->expression);

            return $this;
        };
    }

    public function userAppendOutputToDaily(): \Closure
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, \sprintf('daily-%s', Carbon::now()->format('Y-m-d')), $dirname);
    }

    public function userAppendOutputToWeekly(): \Closure
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, \sprintf('weekly-%s', Carbon::now()->format('Y-W')), $dirname);
    }

    public function userAppendOutputToMonthly(): \Closure
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, \sprintf('monthly-%s', Carbon::now()->format('Y-m')), $dirname);
    }

    public function userAppendOutputToQuarterly(): \Closure
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

    public function userAppendOutputToYearly(): \Closure
    {
        return fn (
            ?string $filename = null,
            ?string $dirname = null
        ): Event => $this->userAppendOutputTo($filename, \sprintf('yearly-%s', Carbon::now()->format('Y')), $dirname);
    }

    public function userAppendOutputTo(): \Closure
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

                            throw_if(
                                empty($this->description),
                                \LogicException::class,
                                "Please incoming the \$filename parameter, Or use the 'name' method before 'userAppendOutputTo'."
                            );

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
                ->before(static function () use ($outputPath): void {
                    Log::build(['path' => $outputPath] + config('logging.channels.single'))->info('>>>>>>>>');
                })
                ->appendOutputTo($outputPath);
        };
    }
}
