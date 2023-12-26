<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\File;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\ScheduleMonitor\Models\MonitoredScheduledTaskLogItem;
use Spatie\ShortSchedule\ShortSchedule;
use Symfony\Component\Console\Output\ConsoleOutput;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     *
     * @noinspection PhpParamsInspection
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly()->withoutOverlapping(60);
        // $schedule->command('backup:clean')->daily()->at('01:00');
        // $schedule->command('backup:run')->daily()->at('02:00');
        // $schedule->command('backup:monitor')->daily()->at('03:00');
        $schedule->command('disposable:update')->weekly()->at('04:00');

        // $schedule->job(function (ConsoleOutput $consoleOutput) {
        //     $consoleOutput->writeln(Inspiring::quote());
        // })->everyMinute();
        //
        // $schedule->call(function (ConsoleOutput $consoleOutput) {
        //     $consoleOutput->writeln(Inspiring::quote());
        // })->everyMinute();

        // $schedule->exec('php', ['-v'])->everyMinute();

        $schedule->command('db:monitor', ['--databases' => 'mysql,pgsql', '--max' => 100])->everyMinute();
        $schedule->command(RunHealthChecksCommand::class)->everyMinute();
        $schedule->command('model:prune', ['--model' => MonitoredScheduledTaskLogItem::class])->daily();
    }

    protected function shortSchedule(ShortSchedule $shortSchedule)
    {
        // this artisan command will run every second
        $shortSchedule->command('inspire')->everySecond();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    private function toOutputPath(string $command): string
    {
        $path = str($command)
            ->classBasename()
            ->snake('-')
            ->replaceLast('-command', '')
            ->finish(date('-Y-m-d'))
            ->append('.log')
            ->prepend(storage_path('logs/commands/'))
            ->toString();

        File::ensureDirectoryExists(\dirname($path));

        return $path;
    }
}
