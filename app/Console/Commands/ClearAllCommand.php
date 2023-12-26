<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all {--f|force : Force clear optimized.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clear optimized all.';

    public function handle(): void
    {
        if (! $this->option('force') && $this->getLaravel()->isProduction()) {
            $this->output->warning('Please use --force option in production.');

            return;
        }

        $this->output->info('Clearing all...');

        $this->call('config:clear', $arguments = ['--ansi' => true, '-v' => true]);
        $this->call('event:clear', $arguments);
        $this->call('route:clear', $arguments);
        $this->call('view:clear', $arguments);
        $this->call('optimize:clear', $arguments);
        $this->call('clear-compiled', $arguments);

        $this->output->success('All cleared.');
    }
}
