<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use Guanguans\LaravelExceptionNotify\Facades\ExceptionNotify;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
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

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('docs:generate-signed-url', function () {
    $signedUrl = URL::temporarySignedRoute('docs', now()->addDays(7));
    $this->info($signedUrl);

    return $this::SUCCESS;
})->purpose('生成接口文档签名地址');

Artisan::command('mail:send {user}', function (string $user): void {
    /** @noinspection ForgottenDebugOutputInspection */
    /** @noinspection DebugFunctionUsageInspection */
    dd($user);
});

/**
 * @var \Illuminate\Foundation\Console\ClosureCommand $this
 */
Artisan::command('deployer:notify {result}', function () {
    // throw_unless(
    //     in_array($this->argument('result'), ['FAILURE', 'SUCCESS']),
    //     InvalidArgumentException::class,
    //     'Invalid result parameters(FAILURE/SUCCESS).'
    // );

    ExceptionNotify::report(str_replace('{result}', $this->argument('result'), $this->signature));

    return $this::SUCCESS;
})->purpose('Deployer notify report.');

Schedule::command('model:prune')->daily()->withoutOverlapping();
