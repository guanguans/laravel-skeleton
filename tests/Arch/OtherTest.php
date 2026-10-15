<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection ClassConstantCanBeUsedInspection */
/** @noinspection ClassnameLiteralInspection */
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
use App\Console\Commands\ClearAllCommand;
use App\Console\Commands\HealthCheckCommand;
use App\Console\Commands\OptimizeAllCommand;
use App\Console\Commands\ReferenceCommand;
use App\Models\AbstractModel;
use App\Models\HttpLog;
use App\Models\JWTUser;
use App\Models\User;

arch()
    ->group(__DIR__, __FILE__)
    // ->skip()
    ->expect('App\Console\Commands')
    ->classes()
    ->toBeClasses()
    ->toExtend(AbstractCommand::class)
    ->ignoring([
        'App\Console\Commands\Concerns\\',
        ClearAllCommand::class,
        HealthCheckCommand::class,
        OptimizeAllCommand::class,
        ReferenceCommand::class,
    ]);

arch()
    ->group(__DIR__, __FILE__)
    // ->skip()
    ->expect('App\Models')
    ->classes()
    ->toBeClasses()
    ->toExtend(AbstractModel::class)
    ->ignoring([
        'App\Models\Concerns\\',
        'App\Models\Pivots\\',
        'App\Models\Scopes\\',
        HttpLog::class,
        JWTUser::class,
        User::class,
    ]);
