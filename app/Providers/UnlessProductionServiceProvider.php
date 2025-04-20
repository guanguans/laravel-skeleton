<?php

/** @noinspection PhpUnusedAliasInspection */

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

use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Support\AggregateServiceProvider;

class UnlessProductionServiceProvider extends AggregateServiceProvider implements ShouldRegisterContract
{
    /** @noinspection PhpFullyQualifiedNameUsageInspection */
    protected $providers = [
        \AndreasElia\PostmanGenerator\PostmanGeneratorServiceProvider::class,
        \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        // \ChrisDiCarlo\LaravelConfigChecker\LaravelConfigCheckerServiceProvider::class,
        // \Enlightn\Enlightn\EnlightnServiceProvider::class,
        // \Guanguans\LaravelSoar\SoarServiceProvider::class,
        \KitLoong\MigrationsGenerator\MigrationsGeneratorServiceProvider::class,
        // \Ladumor\LaravelPwa\PWAServiceProvider::class,
        // \Lanin\Laravel\ApiDebugger\ServiceProvider::class,
        \Laracademy\Generators\GeneratorsServiceProvider::class,
        \LaracraftTech\LaravelSchemaRules\LaravelSchemaRulesServiceProvider::class,
        // \LaraDumps\LaraDumps\LaraDumpsServiceProvider::class,
        \Laravel\Sail\SailServiceProvider::class,
        \Laravel\Telescope\TelescopeServiceProvider::class,
        // \LaravelMigrationGenerator\LaravelMigrationGeneratorProvider::class,
        // \Mortexa\LaravelArkitect\ArkitectServiceProvider::class,
        \Msamgan\LaravelEnvKeysChecker\LaravelEnvKeysCheckerServiceProvider::class,
        \MuhammadHuzaifa\TelescopeGuzzleWatcher\TelescopeGuzzleWatcherServiceProvider::class,
        \NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class,
        \Orangehill\Iseed\IseedServiceProvider::class,
        \PrettyRoutes\ServiceProvider::class,
        \Reliese\Coders\CodersServiceProvider::class,
        // \Salahhusa9\Updater\UpdaterServiceProvider::class,
        // \Scalar\ScalarServiceProvider::class,
        \Shift\FactoryGenerator\FactoryGeneratorServiceProvider::class,
        \SoloTerm\Solo\Providers\SoloServiceProvider::class,
        \Spatie\LaravelErrorSolutions\LaravelErrorSolutionsServiceProvider::class,
        \Spatie\LaravelIgnition\IgnitionServiceProvider::class,
        \Spatie\Stubs\StubsServiceProvider::class,
        \TheDoctor0\LaravelFactoryGenerator\FactoryGeneratorServiceProvider::class,
        \Worksome\Envy\EnvyServiceProvider::class,
    ];

    public function shouldRegister(): bool
    {
        return !$this->app->isProduction();
    }
}
