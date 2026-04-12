<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use App\Console\Commands\Command;
use App\Console\Commands\IdeHelperChoresCommand;
use App\Console\Commands\InitCommand;
use App\Console\Commands\MigrateFromMysqlToSqlite;
use App\Http\Controllers\Api\AuthController;
use App\Jobs\Middleware\EnsureTokenIsValid;
use App\Jobs\Middleware\RateLimitedForJob;
use App\Listeners\AuthSubscriber;
use App\Listeners\ContextSubscriber;
use App\Listeners\PrepareRequestListener;
use App\Listeners\RunCommandInDebugModeListener;
use App\Listeners\TraceEventListener;
use App\Models\BaseModel;
use App\Support\Mixin\SchedulingEventMixin;
use App\Support\Sse\CloseServerSentEventException;

arch()
    ->group(__DIR__, __FILE__)
    ->skip()
    ->preset()->php()->ignoring([
        IdeHelperChoresCommand::class,
        SchedulingEventMixin::class,
        TraceEventListener::class,
    ]);

arch()
    ->group(__DIR__, __FILE__)
    ->skip()
    ->preset()->laravel()->ignoring([
        AuthController::class,
        AuthSubscriber::class,
        BaseModel::class,
        CloseServerSentEventException::class,
        Command::class,
        ContextSubscriber::class,
        EnsureTokenIsValid::class,
        InitCommand::class,
        MigrateFromMysqlToSqlite::class,
        PrepareRequestListener::class,
        RateLimitedForJob::class,
        RunCommandInDebugModeListener::class,
        SchedulingEventMixin::class,
        TraceEventListener::class,
    ]);

arch()
    ->group(__DIR__, __FILE__)
    ->skip()
    ->preset()->security()->ignoring([
        'assert',
        'exec',
    ]);

arch()
    ->group(__DIR__, __FILE__)
    ->skip()
    ->preset()->strict()->ignoring([]);

arch()
    ->group(__DIR__, __FILE__)
    ->skip()
    ->preset()->relaxed()->ignoring([]);

arch('will not use debugging functions')
    ->group(__DIR__, __FILE__)
    // ->throwsNoExceptions()
    ->skip()
    ->expect([
        // 'dd',
        'env',
        'env_explode',
        'env_getcsv',
        'exit',
        'printf',
        'vprintf',
    ])
    // ->each
    ->not->toBeUsed()
    ->ignoring([
        RunCommandInDebugModeListener::class,
        SchedulingEventMixin::class,
    ]);
