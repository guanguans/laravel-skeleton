<?php

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\AggregateServiceProvider;
use KitLoong\MigrationsGenerator\MigrationsGeneratorServiceProvider;
use LaracraftTech\LaravelSchemaRules\LaravelSchemaRulesServiceProvider;
use Laravel\Sail\SailServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider;
use Reliese\Coders\CodersServiceProvider;
use Spatie\LaravelIgnition\IgnitionServiceProvider;
use Worksome\Envy\EnvyServiceProvider;

class DevelopServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        \Lanin\Laravel\ApiDebugger\ServiceProvider::class,
        \LaravelLang\Attributes\ServiceProvider::class,
        \LaravelLang\HttpStatuses\ServiceProvider::class,
        \LaravelLang\Lang\ServiceProvider::class,
        \LaravelLang\Locales\ServiceProvider::class,
        \LaravelLang\Publisher\ServiceProvider::class,
        CodersServiceProvider::class,
        CollisionServiceProvider::class,
        EnvyServiceProvider::class,
        IdeHelperServiceProvider::class,
        IgnitionServiceProvider::class,
        // LaravelSchemaRulesServiceProvider::class,
        MigrationsGeneratorServiceProvider::class,
        SailServiceProvider::class,
        \Laravel\Telescope\TelescopeServiceProvider::class,
        TelescopeServiceProvider::class,
    ];
}
