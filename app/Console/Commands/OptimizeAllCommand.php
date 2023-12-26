<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Process\Process;

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

        Process::fromShellCommandline(sprintf(
            '%s dump-autoload --no-interaction --optimize --ansi -v',
            match ($this->laravel->environment()) {
                'local' => 'composer',
                'testing' => '/usr/bin/php8.1 /usr/local/bin/composer2',
                'production' => 'composer2',
            }
        ))->mustRun(function (string $type, string $line): void {
            $this->output->write($line);
        });

        $this->output->success('All optimized.');
    }
}
