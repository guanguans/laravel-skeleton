<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use App\Console\Commands\ClearAllCommand;
use App\Console\Commands\ClearLogsCommand;
use Illuminate\Foundation\Console\AboutCommand;

it('is http', function (): void {
    $this->get('/')->assertOk();
})->group(__DIR__, __FILE__);

it('is console', function (): void {
    $this->artisan(AboutCommand::class)->assertOk();
    $this->artisan(ClearAllCommand::class)->assertOk();
    $this->artisan(ClearLogsCommand::class)->assertOk();
})->group(__DIR__, __FILE__);
