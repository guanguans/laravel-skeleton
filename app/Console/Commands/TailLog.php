<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class TailLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tail
                            {--file= : Name of the log file to tail}
                            {--lines=0 : Output the last number of lines}
                            {--clear : Clear the terminal screen}
                            {--grep= : Grep specified string}
                            {--debug : Display the underlying tail command}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tail the latest logfile.';

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
        if ($this->option('debug')) {
            $this->info($this->getTailCommand());

            return;
        }

        $this->handleClearOption();

        Process::fromShellCommandline($this->getTailCommand(), storage_path('logs'))
            ->setTty(true)
            ->setTimeout(null)
            ->run(function ($type, $line) {
                $this->handleClearOption();

                $this->output->write($line);
            });

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

    protected function handleClearOption()
    {
        if (! $this->option('clear')) {
            return;
        }

        $this->output->write(sprintf("\033\143\e[3J"));
    }

    protected function getTailFile(): string
    {
        return $this->option('file') ?? '`\ls -t | \head -1`';
    }

    protected function getTailCommand(): string
    {
        return sprintf('\tail -f -n %s "%s" %s', $this->option('lines'), $this->getTailFile(), $this->getTailGrep());
    }

    protected function getTailGrep(): string
    {
        return $this->option('grep') ? sprintf(' | \grep "%s"', $this->option('grep')) : '';
    }
}
