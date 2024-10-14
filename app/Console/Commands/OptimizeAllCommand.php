<?php

namespace App\Console\Commands;

use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ExecutableFinder;

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
            $this->components->warn('Please use --force option in production.');

            return;
        }

        $this->components->info('Optimizing all...');

        $arguments = ['--ansi' => true, '-v' => true];
        foreach ([
            'config:cache',
            'event:cache',
            'route:cache',
            'view:cache',
        ] as $command) {
            try {
                $this->components->task($command, fn () => $this->call($command, $arguments));
            } catch (\Throwable $throwable) {
                // $this->consoleLogger()->error($throwable->getMessage());
                $this->components->error($throwable->getMessage());
            }
        }

        try {
            $command = \sprintf(
                '%s %s dump-autoload --no-interaction --optimize --ansi -v',
                (new ExecutableFinder)->find('php8.1') ?: Application::phpBinary(),
                (new ExecutableFinder)->find('composer2')
                    ?: (new ExecutableFinder)->find('composer')
                    ?: implode(' ', resolve(Composer::class)->findComposer()),
            );

            $this->components->task($command, fn () => $this->processHelperMustRun(
                cmd: $command,
                // verbosity: OutputInterface::VERBOSITY_NORMAL
            ));
        } catch (\Throwable $throwable) {
            $this->components->error($throwable->getMessage());
        }

        $this->components->success('All optimized.');
    }
}
