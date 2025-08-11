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
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

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
        ->merge([
            RouteListCommand::class,
        ])
        // ->dd()
        ->each(function (string $class): void {
            $this->artisan($class, [
                '--no-interaction' => true,
                // '--quiet' => true,
                // '--silent' => true,
            ])->assertOk();
        });
})->group(__DIR__, __FILE__);

it('is command naming', function (): void {
    collect(Artisan::all())
        ->filter(fn (SymfonyCommand $command): bool => str($command::class)->startsWith('App\\Console\\Commands'))
        ->reject(fn (SymfonyCommand $command): bool => str($command->getName())->is([
            // '_complete',
            // 'livewire:configure-s3-upload-cleanup',
            // 'module:v6:migrate',
            // 'saml2:*',
        ]))
        ->each(function (SymfonyCommand $command): void {
            expect(str($command->getName())->replaceMatches('/\d/', '')->explode(':'))->each->toBeKebabCase(
                \sprintf('The command [%s] name [%s] should be kebabcase.', $command::class, $command->getName())
            );
        });
})->group(__DIR__, __FILE__);

it('is route naming', function (): void {
    Artisan::call(RouteListCommand::class, [
        '--except-vendor' => true,
        '--json' => true,
    ]);

    Collection::fromJson(Artisan::output())
        // ->dd()
        // ->whereNotNull('name')
        ->whereNotInStrict('name', [
            // 'ignition.executeSolution',
            // 'ignition.healthCheck',
            // 'ignition.updateConfig',
            // 'sentemails.downloadAttachment',
        ])
        ->whereNotInStrict('uri', [
            'api/users.json',
            // 'api/{fallbackPlaceholder}',
            // '{fallbackPlaceholder}',
            // '_dusk/login/{userId}/{guard?}',
            // 'log-viewer/api/files/{fileIdentifier}',
            // 'log-viewer/api/files/{fileIdentifier}/clear-cache',
            // 'log-viewer/api/files/{fileIdentifier}/download',
            // 'log-viewer/api/files/{fileIdentifier}/download/request',
            // 'log-viewer/api/folders/{folderIdentifier}',
            // 'log-viewer/api/folders/{folderIdentifier}/clear-cache',
            // 'log-viewer/api/folders/{folderIdentifier}/download',
            // 'log-viewer/api/folders/{folderIdentifier}/download/request',
            // '__execute-laravel-error-solution',
            // '_dusk/login/{userId}/{guard?}',
            // '_dusk/logout/{guard?}',
            // '_dusk/user/{guard?}',
            // 'docs.openapi',
            // 'docs.postman',
            // 'docs/api.json',
        ])
        ->each(function (array $route): void {
            expect($route['name'])
                ->not->toBeNull("The route [{$route['method']} {$route['uri']}] name should not be null.");

            expect(str($route['name'])->explode('.'))->each->toBeKebabCase(
                "The route [{$route['method']} {$route['uri']}] name [{$route['name']}] should be kebabcase."
            );

            expect(
                str($route['uri'])
                    ->explode('/')
                    ->reject(static fn (string $segment): bool => str($segment)->isMatch([
                        '/^$/',
                        '/^\{.*\}$/',
                        '/^v[1-9]$/',
                        // '/^_.*$/',
                    ]))
            )->each->toBeKebabCase(
                "The route [{$route['method']} {$route['uri']}] uri [{$route['uri']}] should be kebabcase."
            );
        });
})->group(__DIR__, __FILE__);
