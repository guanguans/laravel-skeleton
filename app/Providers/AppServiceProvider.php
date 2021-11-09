<?php

namespace App\Providers;

use App\Rules\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
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
        $files = (new Finder())
            ->files()
            ->name('*Rule.php')
            ->in($this->app->path('Rules'));

        foreach ($files as $file) {
            $ruleClass = value(function (SplFileInfo $file, $basePath) {
                $class = trim(Str::replaceFirst($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

                return str_replace(
                    [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())).'\\'],
                    ['\\', app()->getNamespace()],
                    ucfirst(Str::replaceLast('.php', '', $class))
                );
            }, $file, base_path());

            try {
                /* @var \App\Rules\Rule $rule */
                $rule = app($ruleClass);
                if (! $rule instanceof Rule) {
                    continue;
                }
            } catch (Throwable $e) {
                continue;
            }

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
