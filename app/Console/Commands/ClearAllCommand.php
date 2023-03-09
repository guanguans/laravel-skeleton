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
        if ($this->getLaravel()->isProduction() && ! $this->option('force')) {
            return self::INVALID;
        }

        $resourceUsage = catch_resource_usage(function () {
            $this->call('config:clear');
            $this->call('event:clear');
            $this->call('route:clear');
            $this->call('view:clear');
            $this->call('optimize:clear');
            $this->call('clear-compiled');
        });

        $this->output->success($resourceUsage);

        return self::SUCCESS;
    }
}
