<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class TailCommand extends Command
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

            return self::SUCCESS;
        }

        $this->handleClearOption();

        Process::fromShellCommandline($this->getTailCommand(), storage_path('logs'))
            ->setTty(true)
            ->setTimeout(null)
            ->run(function ($type, $line) {
                $this->handleClearOption();
                $this->output->write($line);
            });

        return self::SUCCESS;
    }

    protected function handleClearOption()
    {
        if (! $this->option('clear')) {
            return;
        }

        $this->output->write(sprintf("\033\143\e[3J"));
    }

    protected function getTailCommand(): string
    {
        $file = $this->option('file') ?? '`\ls -t | \head -1`';
        $grep = $this->option('grep') ? sprintf(' | \grep "%s"', $this->option('grep')) : '';

        return sprintf('\tail -f -n %s "%s" %s', $this->option('lines'), $file, $grep);
    }
}
