<?php

namespace App\Console;

use Guanguans\LaravelExceptionNotify\Jobs\ReportExceptionJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly()->withoutOverlapping(60);
        // $schedule->command('backup:clean')->daily()->at('01:00');
        // $schedule->command('backup:run')->daily()->at('02:00');
        // $schedule->command('backup:monitor')->daily()->at('03:00');
        $schedule->command('disposable:update')->weekly()->at('04:00');

        // $schedule->job(ReportExceptionJob::class)->everyMinute();
        // $schedule->call(function () {
        //     dump(Inspiring::quote());
        // })->everyMinute();
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
}
