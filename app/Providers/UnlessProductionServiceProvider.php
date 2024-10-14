<?php

namespace App\Providers;

use Illuminate\Support\AggregateServiceProvider;

class UnlessProductionServiceProvider extends AggregateServiceProvider
{
    /**
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    protected $providers = [
        \AndreasElia\PostmanGenerator\PostmanGeneratorServiceProvider::class,
        \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        \ChrisDiCarlo\LaravelConfigChecker\LaravelConfigCheckerServiceProvider::class,
        \Enlightn\Enlightn\EnlightnServiceProvider::class,
        \Guanguans\LaravelSoar\SoarServiceProvider::class,
        \KitLoong\MigrationsGenerator\MigrationsGeneratorServiceProvider::class,
        \Lanin\Laravel\ApiDebugger\ServiceProvider::class,
        \LaraDumps\LaraDumps\LaraDumpsServiceProvider::class,
        \Laracademy\Generators\GeneratorsServiceProvider::class,
        \LaracraftTech\LaravelSchemaRules\LaravelSchemaRulesServiceProvider::class,
        \LaravelMigrationGenerator\LaravelMigrationGeneratorProvider::class,
        \Laravel\Sail\SailServiceProvider::class,
        \Laravel\Telescope\TelescopeServiceProvider::class,
        \Mortexa\LaravelArkitect\ArkitectServiceProvider::class,
        \Msamgan\LaravelEnvKeysChecker\LaravelEnvKeysCheckerServiceProvider::class,
        \NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class,
        \Orangehill\Iseed\IseedServiceProvider::class,
        \PrettyRoutes\ServiceProvider::class,
        \Reliese\Coders\CodersServiceProvider::class,
        \Salahhusa9\Updater\UpdaterServiceProvider::class,
        \Shift\FactoryGenerator\FactoryGeneratorServiceProvider::class,
        \Spatie\LaravelErrorSolutions\LaravelErrorSolutionsServiceProvider::class,
        \Spatie\LaravelIgnition\IgnitionServiceProvider::class,
        \TheDoctor0\LaravelFactoryGenerator\FactoryGeneratorServiceProvider::class,
        \Worksome\Envy\EnvyServiceProvider::class,
    ];
}
