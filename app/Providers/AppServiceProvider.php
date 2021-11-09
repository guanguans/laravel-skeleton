<?php

namespace App\Providers;

use App\Rules\IdCardRule;
use App\Rules\PhoneRule;
use App\Rules\PostalCodeRule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected $rules = [
        IdCardRule::class,
        PhoneRule::class,
        PostalCodeRule::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerNotProductionServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('zh');

        $this->extendValidators();
    }

    /**
     * Register rule.
     */
    protected function extendValidators()
    {
        foreach ($this->rules as $ruleClass) {
            /* @var \App\Rules\Rule $rule */
            $rule = app($ruleClass);

            Validator::extend($rule->getName(), "$ruleClass@passes", $rule->message());
        }
    }

    /**
     * Register local services.
     */
    protected function registerNotProductionServices()
    {
        if ($this->app->isProduction()) {
            return;
        }

        $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        $this->app->register(\Knuckles\Scribe\ScribeServiceProvider::class);
        $this->app->register(\NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class);
    }
}
