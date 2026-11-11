<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Providers;

use App\Console\Commands\ClearAllCommand;
use App\Support\VarDumper\ServerDumper;
use Carbon\CarbonInterval;
use Composer\XdebugHandler\XdebugHandler;
use Illuminate\Console\Application;
use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * @property \Symfony\Component\EventDispatcher\EventDispatcherInterface $symfonyDispatcher
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
            /** @see https://github.com/OussamaMater/Laravel-Tips#tip-266--the-new-optimizes-method */
            $this->optimizes(optimize: 'filament:optimize', key: 'filament');
            $this->addDefaultInputDefinition();
            Application::starting(static function (Application $application): void {});
            ClearAllCommand::prohibit($this->app->isProduction());
            Event::listen(ArtisanStarting::class, static function (ArtisanStarting $artisanStarting): void {});
            ServerDumper::register();
            AboutCommand::add('Application', [
                'Author' => 'guanguans',
                'Homepage' => 'https://github.com/guanguans/laravel-skeleton',
                'Name' => 'laravel-skeleton',
            ]);
            resolve(Kernel::class)->whenCommandLifecycleIsLongerThan(CarbonInterval::milliseconds(1000), static function (): void {});
            Artisan::whenCommandLifecycleIsLongerThan(
                CarbonInterval::milliseconds(1000),
                static function (): void {
                    Log::channel('daily')->warning('Command lifecycle is longer than 1000 milliseconds.');
                }
            );
        });
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {});
    }

    /**
     * @see \Illuminate\Console\Application::getDefaultInputDefinition()
     *
     * @noinspection LaravelUnknownConfigKeyInspection
     */
    private function addDefaultInputDefinition(): void
    {
        $this->app->booted(static function (): void {
            collect(Artisan::all())
                ->each(
                    static fn (SymfonyCommand $symfonyCommand): SymfonyCommand => $symfonyCommand
                        ->addOption(
                            'configuration',
                            null,
                            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                            'Set multiple given configuration values(`--configuration=app.debug=true` or `--configuration app.debug=true`)'
                        )
                        ->addOption('xdebug', null, InputOption::VALUE_NONE, 'Allow running xdebug')
                )
                ->tap(
                    /**
                     * @see \Illuminate\Foundation\Console\Kernel::rerouteSymfonyCommandEvents()
                     * @see \Rector\Console\ConsoleApplication::doRun()
                     * @see https://github.com/infection/infection/blob/master/src/Command/RunCommand.php#L586
                     * @see https://github.com/infection/infection/blob/master/src/Console/XdebugHandler.php
                     * @see https://github.com/phan/phan/blob/v6/src/Phan/CLI.php#L3156
                     * @see https://github.com/phan/phan/blob/v6/src/Phan/Library/Restarter.php
                     * @see https://github.com/vimeo/psalm/blob/6.x/src/Psalm/Internal/Cli/Psalm.php#L948
                     * @see https://github.com/vimeo/psalm/blob/6.x/src/Psalm/Internal/Fork/PsalmRestarter.php
                     */
                    static fn (): null => Event::listen(CommandStarting::class, static function (CommandStarting $commandStarting): void {
                        collect($commandStarting->input->getOption('configuration'))
                            ->mapWithKeys(static function (string $configuration): array {
                                \assert(
                                    str_contains($configuration, '='),
                                    "The configuration option [$configuration] must be formatted as key=value."
                                );
                                [$key, $value] = explode('=', $configuration, 2);

                                return [$key => match (strtolower($value)) {
                                    /** @see \Illuminate\Support\Env::getOption() */
                                    'true', '(true)' => true,
                                    'false', '(false)' => false,
                                    'empty', '(empty)' => '',
                                    'null', '(null)' => null,
                                    default => preg_match('/\A([\'"])(.*)\1\z/', $value, $matches) ? $matches[2] : $value,
                                }];
                            })
                            ->tap(static fn (Collection $configuration): mixed => config($configuration->all()));

                        if (
                            class_exists(XdebugHandler::class)
                            && !$commandStarting->input->hasParameterOption('--xdebug')
                            && !app()->runningUnitTests()
                        ) {
                            $xdebugHandler = new XdebugHandler(config('app.name'));
                            $logger = config('services.xdebug.logger') and $xdebugHandler->setLogger(Log::channel($logger));
                            $mainScript = config('services.xdebug.main_script') and $xdebugHandler->setMainScript($mainScript);
                            config('services.xdebug.persistent', true) and $xdebugHandler->setPersistent();
                            $xdebugHandler->check();
                            unset($xdebugHandler);
                        }
                    })
                );
        });
    }
}
