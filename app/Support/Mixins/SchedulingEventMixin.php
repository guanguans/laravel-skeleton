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
use function Illuminate\Filesystem\join_paths;

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

    public function dailyAppendOutputTo(): \Closure
    {
        return fn (
            ?string $directory = null,
            ?string $filename = null
        ): Event => $this->userAppendOutputTo($directory, $filename, \sprintf('daily-%s', Carbon::now()->format('Y-m-d')));
    }

    public function weeklyAppendOutputTo(): \Closure
    {
        return fn (
            ?string $directory = null,
            ?string $filename = null
        ): Event => $this->userAppendOutputTo($directory, $filename, \sprintf('weekly-%s', Carbon::now()->format('Y-W')));
    }

    public function monthlyAppendOutputTo(): \Closure
    {
        return fn (
            ?string $directory = null,
            ?string $filename = null
        ): Event => $this->userAppendOutputTo($directory, $filename, \sprintf('monthly-%s', Carbon::now()->format('Y-m')));
    }

    public function quarterlyAppendOutputTo(): \Closure
    {
        return fn (
            ?string $directory = null,
            ?string $filename = null
        ): Event => $this->userAppendOutputTo($directory, $filename, \sprintf('quarterly-%s-%s', Carbon::now()->format('Y'), now()->quarter));
    }

    public function yearlyAppendOutputTo(): \Closure
    {
        return fn (
            ?string $directory = null,
            ?string $filename = null
        ): Event => $this->userAppendOutputTo($directory, $filename, \sprintf('yearly-%s', Carbon::now()->format('Y')));
    }

    /**
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    public function userAppendOutputTo(): \Closure
    {
        return function (?string $directory = null, ?string $filename = null, ?string $suffix = null): Event {
            $filename = value(
                function (?string $filename): string {
                    if ($filename) {
                        return $filename;
                    }

                    // artisan
                    if (str($this->command)->contains("'artisan'")) {
                        return str($this->command)->explode(' ')->get(2);
                    }

                    throw_if(
                        empty($this->description),
                        \LogicException::class,
                        'Please input the parameter [$filename], Or call the method [name/description] before call the method [userAppendOutputTo].'
                    );

                    // exec|call|job
                    return $this->description;
                },
                $filename
            );

            $filename = str($filename)
                ->replace(
                    [\DIRECTORY_SEPARATOR, '/', '\\', ':', ' ', ...match (\PHP_OS_FAMILY) {
                        'Windows' => ['<', '>', '/', '\\', '|', ':', '"', '?', '*'],
                        'Darwin' => [':'],
                        'Linux' => ['/'],
                        default => [\DIRECTORY_SEPARATOR],
                    }],
                    '-'
                )
                ->replaceMatches('/-{2,}/', '-')
                ->take(200);

            $location = join_paths(
                $directory ?? join_paths(storage_path('logs'), 'schedules', $filename),
                str($filename)
                    ->when(
                        $suffix,
                        static fn (Stringable $filename): Stringable => $filename->finish('-')->finish($suffix)
                    )
                    ->finish('.log')
            );

            // dump($location);

            return $this
                ->before(static function () use ($location): void {
                    Log::build(['path' => $location] + config('logging.channels.single'))->info('>>>>>>>>');
                })
                ->appendOutputTo($location);
        };
    }
}
