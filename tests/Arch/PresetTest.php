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

use App\Console\Commands\AbstractCommand;
use App\Console\Commands\IdeHelperChoresCommand;
use App\Console\Commands\ReferenceCommand;
use App\Http\Controllers\Api\AuthController;
use App\Jobs\Middleware\RateLimitedForJob;
use App\Listeners\ContextSubscriber;
use App\Listeners\PrepareRequestListener;
use App\Listeners\TraceEventListener;
use App\Models\AbstractModel;
use App\Support\ComposerScripts;
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
        AbstractCommand::class,
        AbstractModel::class,
        AuthController::class,
        CloseServerSentEventException::class,
        ComposerScripts::class,
        ContextSubscriber::class,
        PrepareRequestListener::class,
        RateLimitedForJob::class,
        ReferenceCommand::class,
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
        ComposerScripts::class,
        SchedulingEventMixin::class,
    ]);
