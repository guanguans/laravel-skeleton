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
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

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

it('is command naming', function (): void {
    collect(Artisan::all())
        ->filter(fn (SymfonyCommand $command): bool => str($command::class)->startsWith('App\\Console\\Commands'))
        ->reject(fn (SymfonyCommand $command): bool => str($command->getName())->is([]))
        ->each(
            fn (SymfonyCommand $command) => expect(str($command->getName())->replaceMatches('/\d/', '')->explode(':'))
                ->each
                ->toBeKebabCase(\sprintf('The command [%s] name [%s] should be kebab case.', $command::class, $command->getName()))
        );
})->group(__DIR__, __FILE__);

it('is route naming', function (): void {
    Artisan::call(RouteListCommand::class, ['--except-vendor' => true, '--json' => true]);

    Collection::fromJson(Artisan::output())
        // ->dd()
        // ->whereNotNull('name')
        ->whereNotInStrict('name', [])
        ->whereNotInStrict('uri', [
            'api/users.json',
        ])
        ->each(function (array $route): void {
            expect($route['name'])->not->toBeNull("The route [{$route['method']} {$route['uri']}] name should not be null.");

            expect(str($route['name'])->explode('.'))->each->toBeKebabCase(
                "The route [{$route['method']} {$route['uri']}] name [{$route['name']}] should be kebab case."
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
                "The route [{$route['method']} {$route['uri']}] uri [{$route['uri']}] should be kebab case."
            );
        });
})->group(__DIR__, __FILE__);
