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

namespace App\Console\Commands;

use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;

class ListSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:lists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all scheduled tasks';

    /**
     * Create a new command instance.
     */
    public function __construct(private readonly Schedule $schedule)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (\count($this->schedule->events()) <= 0) {
            $this->info('No Scheduled Commands Found');

            return;
        }

        $events = collect($this->schedule->events())->map(fn ($event): array => [
            'description' => $event->description ?: 'N/A',
            'command' => ltrim(strtok(Str::after($event->command, "'artisan'"), ' ')),
            'schedule' => $event->expression,
            'upcoming' => $this->upcoming($event),
            'timezone' => $event->timezone ?: config('app.timezone'),
            'overlaps' => $event->withoutOverlapping ? 'No' : 'Yes',
            'maintenance' => $event->evenInMaintenanceMode ? 'Yes' : 'No',
            'one_server' => $event->onOneServer ? 'Yes' : 'No',
            'in_background' => $event->runInBackground ? 'Yes' : 'No',
        ]);

        $this->table(
            ['Description', 'Command', 'Schedule', 'Upcoming', 'Timezone', 'Overlaps?', 'In Maintenance?', 'One Server?', 'In Background?'],
            $events
        );
    }

    /**
     * Get Upcoming schedule.
     *
     * @throws \Exception
     */
    protected function upcoming(mixed $event): string
    {
        $date = Carbon::now();

        if ($event->timezone) {
            $date->setTimezone($event->timezone);
        }

        return (new CronExpression($event->expression))->getNextRunDate($date->toDateTimeString())->format('Y-m-d H:i:s');
    }
}
