<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class TailLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tail {file? : 指定的文件。}
                                 {lines=-10 : 输出最后 N 行，而非默认的最后 10 行。}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tail a log file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file') or $file = $this->guessLogFile(storage_path('logs'));

        passthru(sprintf('tail -f %s -n %s -v', $file, $this->argument('lines')));

        return 0;
    }

    /**
     * @param  null  $dirs
     * @param  string  $patterns
     */
    protected function guessLogFile($dirs, $patterns = '*.log')
    {
        $files = Finder::create()
            ->files()
            ->name($patterns)
            // ->sortByName()
            ->sortByModifiedTime()
            ->reverseSorting()
            ->in($dirs);

        foreach ($files as $file) {
            return $file->getPathname();
        }
    }
}
