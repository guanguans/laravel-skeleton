<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;

class GenerateSignedDocsUrlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:generate-signed-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成接口文档签名地址';

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
        $signedUrl = URL::temporarySignedRoute('docs', now()->addDays(30));

        $this->info($signedUrl);

        return self::SUCCESS;
    }
}
