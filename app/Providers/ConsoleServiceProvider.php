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
use Carbon\CarbonInterval;
use Composer\XdebugHandler\XdebugHandler;
use Illuminate\Console\Application;
use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * @property EventDispatcherInterface $symfonyDispatcher
 */
final class ConsoleServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, function (): void {
            AboutCommand::add('Application', [
                'author' => 'guanguans',
                'homepage' => 'https://github.com/guanguans/laravel-skeleton',
                'name' => 'laravel-skeleton',
            ]);

            /**
             * @see https://github.com/OussamaMater/Laravel-Tips#tip-266--the-new-optimizes-method
             */
            $this->optimizes(
                optimize: 'filament:optimize',
                key: 'filament'
            );

            Event::listen(ArtisanStarting::class, static function (ArtisanStarting $artisanStarting): void {});
            Artisan::whenCommandLifecycleIsLongerThan(CarbonInterval::seconds(3), static function (): void {});
            Application::starting(static function (Application $application): void {});
            $this->addDefaultInputDefinition();

            $this->whenProduction();
        });
    }

    private function never(): void
    {
        $this->whenever(false, function (): void {
            $this->app->booted(static function (): void {
                /**
                 * @see \Illuminate\Foundation\Console\Kernel::rerouteSymfonyCommandEvents()
                 */
                Event::listen(CommandStarting::class, RunCommandInDebugModeListener::class);
            });
        });
    }

    private function whenProduction(): void
    {
        $this->whenever($this->app->isProduction(), static function (): void {
            ClearAllCommand::prohibit();
        });
    }

    /**
     * @see \Illuminate\Console\Application::getDefaultInputDefinition()
     */
    private function addDefaultInputDefinition(): void
    {
        $this->app->booted(function (): void {
            collect(Artisan::all())
                ->each(static function (SymfonyCommand $command): void {
                    $command
                        ->addOption('xdebug', null, InputOption::VALUE_NONE, 'Display xdebug output')
                        ->addOption(
                            'configuration',
                            null,
                            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                            'Used to dynamically pass one or more configuration key-value pairs(e.g. `--configuration=app.name=guanguans` or `--configuration app.name=guanguans`).',
                        );
                })
                ->tap(function (): void {
                    /**
                     * @see \Illuminate\Foundation\Console\Kernel::rerouteSymfonyCommandEvents()
                     * @see \Rector\Console\ConsoleApplication::doRun()
                     */
                    Event::listen(CommandStarting::class, function (CommandStarting $commandStarting): void {
                        if (!$commandStarting->input->hasParameterOption('--xdebug') && !$this->app->runningUnitTests()) {
                            $xdebugHandler = new XdebugHandler(config('app.name'));
                            $xdebugHandler->setPersistent();
                            $xdebugHandler->check();
                            unset($xdebugHandler);
                        }

                        collect($commandStarting->input->getOption('configuration'))
                            // ->dump()
                            ->mapWithKeys(static function (string $configuration): array {
                                Assert::contains($configuration, '=', "The configureable option [$configuration] must be formatted as key=value.");

                                [$key, $value] = str($configuration)->explode('=', 2)->all();

                                return [$key => $value];
                            })
                            ->tap(static fn (Collection $configuration): mixed => config($configuration->all()));
                    });
                });
        });
    }
}
