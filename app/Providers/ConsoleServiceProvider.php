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
use App\Listeners\RunCommandInDebugModeListener;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @property EventDispatcherInterface $symfonyDispatcher
 */
class ConsoleServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        $this->forever();
        $this->whenProduction();
    }

    private function forever(): void
    {
        AboutCommand::add('Application', [
            'name' => 'laravel-skeleton',
            'author' => 'guanguans',
            'github' => 'https://github.com/guanguans/laravel-skeleton',
            'license' => 'MIT License',
        ]);

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

    private function whenProduction(): void
    {
        $this->when($this->app->isProduction(), static function (): void {
            ClearAllCommand::prohibit();
        });
    }
}
