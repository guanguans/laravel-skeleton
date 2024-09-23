<?php

namespace App\Console;

use App\Console\Commands\ClearLogsCommand;
use Arifhp86\ClearExpiredCacheFile\ClearExpiredCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\File;
use jdavidbakr\LaravelCacheGarbageCollector\LaravelCacheGarbageCollector;
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
        LaravelCacheGarbageCollector::class,
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
        $schedule->command('inspire')->daily()->atRandom('07:15', '11:42')->withoutOverlapping(60);
        $schedule->command('backup:clean')->daily()->at('05:15')->withoutOverlapping();
        $schedule->command('backup:run')->daily()->at('05:30')->withoutOverlapping();
        $schedule->command('backup:monitor')->daily()->at('05:45')->withoutOverlapping();
        $schedule->command('model:prune', ['--model' => MonitoredScheduledTaskLogItem::class])->daily()->withoutOverlapping();
        $schedule->command('telescope:prune')->daily()->skip($this->app->isProduction())->withoutOverlapping();
        $schedule->command(ClearLogsCommand::class)->daily()->appendOutputTo($this->toOutputPath(ClearLogsCommand::class))->withoutOverlapping();
        $schedule->command(ClearExpiredCommand::class)->daily()->appendOutputTo($this->toOutputPath(ClearExpiredCommand::class))->withoutOverlapping();
        $schedule->command('disposable:update')->weekly()->at('04:00');
        $schedule->command('db:monitor', ['--databases' => 'mysql,pgsql', '--max' => 100])->everyMinute();
        $schedule->command(RunHealthChecksCommand::class)->everyMinute();
        $schedule->command(LaravelCacheGarbageCollector::class)->daily();

        // $schedule->job(function (ConsoleOutput $consoleOutput) {
        //     $consoleOutput->writeln(Inspiring::quote());
        // })->everyMinute();
        //
        // $schedule->call(function (ConsoleOutput $consoleOutput) {
        //     $consoleOutput->writeln(Inspiring::quote());
        // })->everyMinute();

        // $schedule->exec('php', ['-v'])->everyMinute();
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
            ->finish(\Carbon\Carbon::now()->format('-Y-m-d'))
            ->append('.log')
            ->prepend(storage_path('logs/commands/'))
            ->toString();

        File::ensureDirectoryExists(\dirname($path));

        return $path;
    }
}
