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

namespace App\Providers;

use App\Console\Commands\ClearAllCommand;
use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Traits\Conditionable;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @property EventDispatcherInterface $symfonyDispatcher
 */
class WhenRunningInConsoleServiceProvider extends AggregateServiceProvider implements ShouldRegisterContract
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @noinspection ClassOverridesFieldOfSuperClassInspection
     * @noinspection PropertyInitializationFlawsInspection
     */
    protected $providers = [
    ];

    public function shouldRegister(): bool
    {
        return $this->app->runningInConsole();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        $this->whenever($this->app->runningInConsole(), static function (): void {
            AboutCommand::add('Application', [
                'Name' => 'laravel-skeleton',
                'author' => 'guanguans',
                'github' => 'https://github.com/guanguans/laravel-skeleton',
                'license' => 'MIT License',
            ]);
        });

        ClearAllCommand::prohibit(app()->isProduction());

        /** @see https://github.com/OussamaMater/Laravel-Tips#tip-266--the-new-optimizes-method */
        $this->optimizes(
            optimize: 'filament:optimize',
            // Defaults to the service provider name without "ServiceProvider" suffix
            key: 'filament'
        );

        // $this->app->booted(function (): void {
        //     (fn () => $this->symfonyDispatcher->addListener(
        //         ConsoleEvents::COMMAND,
        //         new RunCommandInDebugModeListener
        //     ))->call($this->app->make(Kernel::class));
        // });
    }
}
