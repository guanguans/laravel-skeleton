<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('docs:generate-signed-url', function () {
    $signedUrl = URL::temporarySignedRoute('docs', now()->addDays(7));
    $this->info($signedUrl);

    return $this::SUCCESS;
})->purpose('生成接口文档签名地址');

Artisan::command('deployer:notify {result}', function () {
    /** @var \Illuminate\Foundation\Console\ClosureCommand $this */
    if (! in_array($this->argument('result'), ['FAILURE', 'SUCCESS'])) {
        throw new InvalidArgumentException('Invalid result parameters(FAILURE/SUCCESS).');
    }

    exception_notify_report(str_replace('{result}', $this->argument('result'), $this->signature));

    return $this::SUCCESS;
})->purpose('Deployer notify report.');
