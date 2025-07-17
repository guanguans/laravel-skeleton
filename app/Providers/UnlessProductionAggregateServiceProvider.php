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

use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Traits\Conditionable;

final class UnlessProductionAggregateServiceProvider extends AggregateServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @noinspection ClassConstantCanBeUsedInspection
     * @noinspection ClassnameLiteralInspection
     * @noinspection ClassOverridesFieldOfSuperClassInspection
     * @noinspection SpellCheckingInspection
     */
    protected $providers = [
        'Amirami\\Localizator\\ServiceProvider',
        'AndreasElia\\PostmanGenerator\\PostmanGeneratorServiceProvider',
        'Barryvdh\\LaravelIdeHelper\\IdeHelperServiceProvider',
        'BeyondCode\QueryDetector\QueryDetectorServiceProvider',
        'Dedoc\\Scramble\\ScrambleServiceProvider',
        'DragonCode\\MigrateDB\\ServiceProvider',
        'Guanguans\\LaravelSoar\\SoarServiceProvider',
        'JMac\\Testing\\AdditionalAssertionsServiceProvider',
        'Josezenem\\LaravelMakeMigrationPivot\\LaravelMakeMigrationPivotServiceProvider',
        'KitLoong\\MigrationsGenerator\\MigrationsGeneratorServiceProvider',
        'Knuckles\\Scribe\\ScribeServiceProvider',
        'LaracraftTech\\LaravelSchemaRules\\LaravelSchemaRulesServiceProvider',
        'Laravel\\Dusk\\DuskServiceProvider',
        'Laravel\\Pail\\PailServiceProvider',
        'Laravel\\Sail\\SailServiceProvider',
        'Laravel\\Telescope\\TelescopeServiceProvider',
        'Lubusin\\Decomposer\\DecomposerServiceProvider',
        'Msamgan\\LaravelEnvKeysChecker\\LaravelEnvKeysCheckerServiceProvider',
        'MuhammadHuzaifa\\TelescopeGuzzleWatcher\\TelescopeGuzzleWatcherServiceProvider',
        'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
        'Orangehill\\Iseed\\IseedServiceProvider',
        'PrettyRoutes\\ServiceProvider',
        'Rakutentech\\LaravelRequestDocs\\LaravelRequestDocsServiceProvider',
        'Reliese\\Coders\\CodersServiceProvider',
        'Scalar\\ScalarServiceProvider',
        'Shift\\FactoryGenerator\\FactoryGeneratorServiceProvider',
        'SoloTerm\\Dumps\\Providers\\DumpServiceProvider',
        'SoloTerm\\Solo\\Providers\\SoloServiceProvider',
        'Spatie\\HorizonWatcher\\HorizonWatcherServiceProvider',
        'Spatie\\LaravelErrorSolutions\\LaravelErrorSolutionsServiceProvider',
        'Spatie\\LaravelIgnition\\IgnitionServiceProvider',
        'Spatie\\Stubs\\StubsServiceProvider',
        'Sti3bas\\ScoutArray\\ScoutArrayEngineServiceProvider',
        'TheDoctor0\\LaravelFactoryGenerator\\FactoryGeneratorServiceProvider',
        'Vcian\\LaravelDBAuditor\\Providers\\DBAuditorServiceProvider',
        'Worksome\\Envy\\EnvyServiceProvider',
        'Worksome\\RequestFactories\\RequestFactoriesServiceProvider',
    ];

    public function register(): void
    {
        if (!$this->app->isProduction()) {
            parent::register();
        }
    }

    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    private function ever(): void
    {
        $this->whenever(true, static function (): void {});
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {});
    }
}
