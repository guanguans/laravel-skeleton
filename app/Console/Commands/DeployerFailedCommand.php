<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeployerFailedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployer:failed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deployer failed';

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
        // todo something
        exception_notify_report($this->description);

        return 0;
    }
}
