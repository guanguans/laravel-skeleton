<?php

namespace App\Providers;

use App\Rules\Rule;
use App\Support\Macros\BlueprintMacro;
use App\Support\Macros\CollectionMacro;
use App\Support\Macros\GrammarMacro;
use App\Support\Macros\QueryBuilderMacro;
use App\Support\Macros\RequestMacro;
use App\Support\Macros\StringableMacro;
use App\Support\Macros\StrMacro;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var string[]
     */
    public $singletons = [
        \App\Support\Macros\RequestMacro::class => \App\Support\Macros\RequestMacro::class,
        \App\Support\Macros\CollectionMacro::class => \App\Support\Macros\CollectionMacro::class,
        \App\Support\Macros\StrMacro::class => \App\Support\Macros\StrMacro::class,
        \App\Support\Macros\StringableMacro::class => \App\Support\Macros\StringableMacro::class,
        \App\Support\Macros\QueryBuilderMacro::class => \App\Support\Macros\QueryBuilderMacro::class,
        \App\Support\Macros\BlueprintMacro::class => \App\Support\Macros\BlueprintMacro::class,
        \App\Support\Macros\GrammarMacro::class => \App\Support\Macros\GrammarMacro::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->registerGlobalFunctions();
        $this->registerNotProductionServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 预防 N+1 查询问题
        Model::preventLazyLoading(! $this->app->isProduction());
        Schema::defaultStringLength(191);
        Carbon::setLocale('zh');
        JsonResource::withoutWrapping();
        // Paginator::useBootstrap();
        $this->bootProduction();
        $this->registerMacros();
        $this->extendValidators($this->app->path('Rules'));
        // $this->extendView();
        ConvertEmptyStringsToNull::skipWhen(function (Request $request) {
            return $request->is('api/*');
        });
    }

    /**
     * Register rule.
     */
    protected function extendValidators($dirs, $name = '*Rule.php', $notName = ['Rule.php', 'RegexRule.php'])
    {
        $files = Finder::create()
            ->files()
            ->name($name)
            ->notName($notName)
            ->in($dirs);

        foreach ($files as $file) {
            $ruleClass = transform($file, function (SplFileInfo $file) {
                $class = trim(Str::replaceFirst(base_path(), '', $file->getRealPath()), DIRECTORY_SEPARATOR);

                return str_replace(
                    [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())).'\\'],
                    ['\\', app()->getNamespace()],
                    ucfirst(Str::replaceLast('.php', '', $class))
                );
            });

            if (! is_subclass_of($ruleClass, Rule::class)) {
                throw new RuntimeException("$ruleClass must be a subclass of App\Rules\Rule");
            }

            /** @var \App\Rules\Rule $rule */
            Validator::extend(($rule = app($ruleClass))->getName(), "$ruleClass@passes", $rule->message());
        }
    }

    protected function registerGlobalFunctions()
    {
        $files = glob($this->app->path('Support/*helpers.php'));
        foreach ($files as $file) {
            require_once $file;
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

        $this->app->register(\NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class);
        $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        $this->app->register(\Lanin\Laravel\ApiDebugger\ServiceProvider::class);
    }

    /**
     * Register macros.
     */
    protected function registerMacros()
    {
        Request::mixin($this->app->make(RequestMacro::class));
        Collection::mixin($this->app->make(CollectionMacro::class));
        QueryBuilder::mixin($queryBuilderMacro = $this->app->make(QueryBuilderMacro::class));
        EloquentBuilder::mixin($queryBuilderMacro);
        Relation::mixin($queryBuilderMacro);
        Str::mixin($this->app->make(StrMacro::class));
        Stringable::mixin($this->app->make(StringableMacro::class));
        Blueprint::mixin($this->app->make(BlueprintMacro::class));
        Grammar::mixin($this->app->make(GrammarMacro::class));
    }

    /**
     * Boot Production.
     */
    protected function bootProduction()
    {
        if (! $this->app->isProduction()) {
            return;
        }

        // URL::forceScheme('https');
        // $this->app->make(Request::class)->server->set('HTTPS', 'on');
        // $this->app->make(Request::class)->server->set('SERVER_PORT', 443);
    }

    protected function extendView()
    {
        $this->app->make('view')->composer('*', function ($view) {
            $view->with('request', $this->app->make(Request::class));
        });

        $this->app->make('view')->composer('*', function ($view) {
            $view->with('user', $this->app->make('auth')->user());
        });

        $this->app->make('view')->composer('*', function ($view) {
            $view->with('config', $this->app->make('config'));
        });

        $this->app->make('view')->creator('*', function ($view) {
            $view->with('request', $this->app->make(Request::class));
        });

        $this->app->make('view')->share('request', $this->app->make(Request::class));
        $this->app->make('view')->share('user', $this->app->make('auth')->user());
        $this->app->make('view')->share('config', $this->app->make('config'));
    }
}
