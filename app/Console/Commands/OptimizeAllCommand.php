<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder;

class OptimizeAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:all {--f|force : Force optimize.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize all.';

    public function handle(): void
    {
        if (! $this->option('force') && $this->getLaravel()->isProduction()) {
            $this->output->warning('Please use --force option in production.');

            return;
        }

        $this->output->info('Optimizing all...');

        $this->call('config:cache', $arguments = ['--ansi' => true, '-v' => true]);
        $this->call('event:cache', $arguments);
        $this->call('route:cache', $arguments);

        try {
            $this->call('view:cache', $arguments);
        } catch (DirectoryNotFoundException $directoryNotFoundException) {
            $this->output->error($directoryNotFoundException->getMessage());
        }

        $command = \sprintf(
            '%s %s dump-autoload --no-interaction --optimize --ansi -v',
            (new ExecutableFinder)->find('php8.1') ?: (new PhpExecutableFinder)->find(),
            (new ExecutableFinder)->find('composer2') ?: (new ExecutableFinder)->find('composer'),
        );

        $this->output->info("Running [$command] ...");

        Process::run($command, function (string $type, string $line): void {
            $this->output->write($line);
        })->throw();

        $this->output->success('All optimized.');
    }
}
