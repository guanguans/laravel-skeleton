<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class ClearAllCommand extends Command implements Isolatable, PromptsForMissingInput
{
    use Prohibitable;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all {--f|force : Force clear optimized.}';

    protected $aliases = ['ca'];

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

        // \function_exists('opcache_invalidate') and opcache_invalidate(app()->getCachedConfigPath());

        $this->output->success('All cleared.');
        // $this->output->info('ℹ️ All cleared.');
        // $this->output->info('⏳ Please wait...');
        // $this->output->warning('⚠️ All cleared.');
        // $this->output->error('❌  All cleared.');
        // $this->output->success('✅  All cleared.');

        // $this->fail('Something went wrong.');

        // $this->trap([SIGTERM, SIGQUIT], function (int $signal) {
        //     $this->shouldKeepRunning = false;
        //     dump($signal); // SIGTERM / SIGQUIT
        // });
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'user' => 'Which user ID should receive the mail?',
        ];
    }
}
