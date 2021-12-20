<?php

namespace App\Providers;

use App\Rules\Rule;
use App\Support\Macros\CollectionMacro;
use App\Support\Macros\RequestMacro;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var string[]
     */
    public $singletons = [
        \App\Support\Response::class => \App\Support\Response::class,
        \App\Support\Macros\RequestMacro::class => \App\Support\Macros\RequestMacro::class,
        \App\Support\Macros\CollectionMacro::class => \App\Support\Macros\CollectionMacro::class
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
        Schema::defaultStringLength(191);
        Carbon::setLocale('zh');
        JsonResource::withoutWrapping();
        $this->registerMacros();
        $this->extendValidators($this->app->path('Rules'));
    }

    /**
     * Register rule.
     */
    protected function extendValidators($dirs, $patterns = '*Rule.php')
    {
        $files = Finder::create()
            ->files()
            ->name($patterns)
            ->in($dirs);

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
        $this->app->register(\NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class);
    }

    /**
     * Register macros.
     */
    protected function registerMacros()
    {
        Request::mixin($this->app->make(RequestMacro::class));
        Collection::mixin($this->app->make(CollectionMacro::class));
    }
}
