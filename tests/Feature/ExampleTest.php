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

use App\Console\Commands\CachePruneCommand;
use App\Console\Commands\CheckServiceProviderCommand;
use App\Console\Commands\ClearAllCommand;
use App\Console\Commands\ClearLogsCommand;
use App\Console\Commands\FindDumpStatementCommand;
use App\Console\Commands\FindStaticMethodsCommand;
use App\Console\Commands\GenerateSitemapCommand;
use App\Console\Commands\HealthCheckCommand;
use App\Console\Commands\IdeHelperChoresCommand;
use App\Console\Commands\InflectorCommand;
use App\Console\Commands\InitCommand;
use App\Console\Commands\MigrateFromMysqlToSqlite;
use App\Console\Commands\OpcacheUrlCommand;
use App\Console\Commands\OptimizeAllCommand;
use App\Console\Commands\PerformDatabaseBackupCommand;
use App\Console\Commands\ShowUnsupportedRequiresCommand;
use App\Console\Commands\UpdateReadmeCommand;

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */
it('is http', function (): void {
    $this->get('/')->assertOk();

    $this->get('/api/v1/ping')
        // ->ddBody()
        // ->ddHeaders()
        // ->ddJson()
        ->assertOk()
        ->assertJsonStructure();

    $this->get('api/v1/ping?bad=1')
        // ->ddBody()
        // ->ddHeaders()
        // ->ddJson()
        ->assertBadRequest()
        ->assertJsonStructure();
})->group(__DIR__, __FILE__);

it('is console', function (): void {
    classes(
        static fn (
            string $class,
            string $file
        ): bool => str($class)->is('App\\Console\\Commands\\*') && str($file)->is('*/../../app/Console/Commands/*')
    )
        ->filter(fn (ReflectionClass $reflectionClass): bool => $reflectionClass->isInstantiable())
        ->reject(fn (ReflectionClass $reflectionClass): bool => str($reflectionClass->getName())->is([
            FindDumpStatementCommand::class,
            FindStaticMethodsCommand::class,
            GenerateSitemapCommand::class,
            IdeHelperChoresCommand::class,
            InflectorCommand::class,
            InitCommand::class,
            MigrateFromMysqlToSqlite::class,
            OptimizeAllCommand::class,

            // CachePruneCommand::class,
            CheckServiceProviderCommand::class,
            // ClearAllCommand::class,
            // ClearLogsCommand::class,
            // HealthCheckCommand::class,
            // OpcacheUrlCommand::class,
            PerformDatabaseBackupCommand::class,
            // ShowUnsupportedRequiresCommand::class,
            // UpdateReadmeCommand::class,
        ]))
        ->keys()
        // ->dd()
        ->each(function (string $class): void {
            $this->artisan($class, [
                '--no-interaction' => true,
                // '--quiet' => true,
                // '--silent' => true,
            ])->assertOk();
        });
})->group(__DIR__, __FILE__);
