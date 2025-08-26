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

use App\Console\Commands\Command;
use App\Console\Commands\InitCommand;
use App\Console\Commands\MigrateFromMysqlToSqlite;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\LogHttp;
use App\Jobs\Middleware\EnsureTokenIsValid;
use App\Jobs\Middleware\RateLimitedForJob;
use App\Listeners\AuthSubscriber;
use App\Listeners\ContextSubscriber;
use App\Listeners\PrepareRequestListener;
use App\Listeners\RunCommandInDebugModeListener;
use App\Listeners\TraceEventListener;
use App\Models\BaseModel;
use App\Support\Mixins\SchedulingEventMixin;
use App\Support\Sse\CloseServerSentEventException;

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */
arch()->group(__DIR__, __FILE__)->skip()->preset()->php()->ignoring([
    'debug_backtrace',
    SchedulingEventMixin::class,
]);

arch()->group(__DIR__, __FILE__)->preset()->security()->ignoring([
    'array_rand',
    'assert',
    'tempnam',
]);

arch()->group(__DIR__, __FILE__)->skip()->preset()->laravel()->ignoring([
    Command::class,
    InitCommand::class,
    MigrateFromMysqlToSqlite::class,
    AuthController::class,
    LogHttp::class,
    EnsureTokenIsValid::class,
    RateLimitedForJob::class,
    AuthSubscriber::class,
    ContextSubscriber::class,
    PrepareRequestListener::class,
    RunCommandInDebugModeListener::class,
    TraceEventListener::class,
    BaseModel::class,
    SchedulingEventMixin::class,
    CloseServerSentEventException::class,
]);

arch()->group(__DIR__, __FILE__)->skip()->preset()->strict()->ignoring([
]);

arch()->group(__DIR__, __FILE__)->skip()->preset()->relaxed()->ignoring([
]);

arch('will not use debugging functions')
    // ->throwsNoExceptions()
    ->group(__DIR__, __FILE__)
    ->expect([
        'dd',
        'die',
        'dump',
        'echo',
        'env',
        'env_explode',
        'env_getcsv',
        'exit',
        'print',
        'print_r',
        'printf',
        'ray',
        'trap',
        'var_dump',
        'var_export',
        'vprintf',
    ])
    // ->each
    ->not->toBeUsed()
    ->ignoring([
        RunCommandInDebugModeListener::class,
        SchedulingEventMixin::class,
    ]);
