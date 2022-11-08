<?php

namespace App\Providers;

use App\Rules\DefaultRule;
use App\Rules\ImplicitRule;
use App\Rules\InstanceofRule;
use App\Rules\Rule;
use App\Support\Macros\BlueprintMacro;
use App\Support\Macros\CollectionMacro;
use App\Support\Macros\GrammarMacro;
use App\Support\Macros\MySqlGrammarMacro;
use App\Support\Macros\QueryBuilderMacro;
use App\Support\Macros\RequestMacro;
use App\Support\Macros\StringableMacro;
use App\Support\Macros\StrMacro;
use App\Support\Macros\WhereNotMacro;
use App\Traits\Conditionable;
use App\View\Components\AlertComponent;
use App\View\Composers\RequestComposer;
use App\View\Creators\RequestCreator;
use ArgumentCountError;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AppServiceProvider extends ServiceProvider
{
    use Conditionable;

    /**
     * All of the container bindings that should be registered.
     *
     * @var string[]
     */
    public $bindings = [];

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
        \App\Support\Macros\MySqlGrammarMacro::class => \App\Support\Macros\MySqlGrammarMacro::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->while(true, function () {
            $this->registerGlobalFunctionsFrom($this->app->path('Support/*helpers.php'));
        });

        $this->unless($this->app->isProduction(), function () {
            $this->registerNotProductionServiceProviders();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->while(true, function () {
            // 低版本 MySQL(< 5.7.7) 或 MariaDB(< 10.2.2)，则可能需要手动配置迁移生成的默认字符串长度，以便按顺序为它们创建索引。
            Schema::defaultStringLength(191);
            Carbon::setLocale(config('app.locale'));
            JsonResource::withoutWrapping();
            Paginator::useBootstrap();
            // Blade::withoutDoubleEncoding(); // 禁用 HTML 实体双重编码
            $this->registerMacros();
            $this->extendValidator();
            $this->extendValidatorFrom($this->app->path('Rules'));
            $this->extendView();
            ConvertEmptyStringsToNull::skipWhen(function (Request $request) {
                return $request->is('api/*');
            });
        });

        $this->while($this->app->isProduction(), function () {
            // URL::forceScheme('https');
            // $this->app->make(Request::class)->server->set('HTTPS', 'on');
            // $this->app->make(Request::class)->server->set('SERVER_PORT', 443);
        });

        $this->while($this->app->environment('testing'), function () {
            // Http::preventStrayRequests(); // Preventing Stray Requests
        });

        $this->unless($this->app->isProduction(), function () {
            // Model::shouldBeStrict(); // Eloquent 严格模式
            Model::preventLazyLoading(); // 预防 N+1 查询问题
            // Model::preventSilentlyDiscardingAttributes(); // 防止模型静默丢弃不在 fillable 中的字段
            // Model::preventsAccessingMissingAttributes(); // Triggers MissingAttributeException
            // DB::handleExceedingCumulativeQueryDuration();
        });
    }

    protected function registerGlobalFunctionsFrom(string $pattern)
    {
        $files = glob($pattern);
        foreach ($files as $file) {
            require_once $file;
        }
    }

    protected function registerNotProductionServiceProviders()
    {
        $this->app->register(\NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class);
        $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        $this->app->register(\Lanin\Laravel\ApiDebugger\ServiceProvider::class);
        $this->app->register(\Reliese\Coders\CodersServiceProvider::class);
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
        MySqlGrammar::mixin($this->app->make(MySqlGrammarMacro::class));
        WhereNotMacro::addMacro();
    }

    protected function extendValidator(): void
    {
        // 默认值规则
        Validator::extendImplicit('default', function (string $attribute, $value, array $parameters, \Illuminate\Validation\Validator $validator) {
            return (new DefaultRule($parameters[0] ?? $value))
                ->setValidator($validator)
                ->passes($attribute, $value);
        });

        // instanceof 规则
        Validator::extend('instanceof', function (string $attribute, $value, array $parameters, \Illuminate\Validation\Validator $validator) {
            if (empty($parameters)) {
                throw new ArgumentCountError(
                    sprintf(
                        'Too few arguments to function App\Rules\InstanceofRule::__construct(), 0 passed in %s on line %s and exactly 1 expected',
                        __FILE__,
                        __LINE__
                    )
                );
            }

            return (new InstanceofRule($parameters[0]))
                ->passes($attribute, $value);
        }, (new InstanceofRule(''))->message());
    }

    /**
     * Register rule.
     */
    protected function extendValidatorFrom(
        $dirs,
        $name = '*Rule.php',
        $notName = [
            'Rule.php',
            'RegexRule.php',
            'ImplicitRule.php',
            'RegexImplicitRule.php',
            'InstanceofRule.php'
        ]
    ) {
        foreach (Finder::create()->files()->name($name)->notName($notName)->in($dirs) as $splFileInfo) {
            $ruleClass = transform($splFileInfo, function (SplFileInfo $splFileInfo) {
                $class = trim(Str::replaceFirst(base_path(), '', $splFileInfo->getRealPath()), DIRECTORY_SEPARATOR);

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
            $rule = app($ruleClass);

            if (
                Arr::first([DataAwareRule::class, ValidatorAwareRule::class], function ($class) use ($rule) {
                    return $rule instanceof $class;
                })
            ) {
                continue;
            }

            if ($rule instanceof ImplicitRule) {
                Validator::extendImplicit($rule->getName(), "$ruleClass@passes", $rule->message());
            }

            Validator::extend($rule->getName(), "$ruleClass@passes", $rule->message());
        }
    }

    protected function extendView()
    {
        // 合成器
        $this->app->make('view')->composer('*', RequestComposer::class);
        $this->app->make('view')->composer('*', function (View $view) {
            $view->with('request', $this->app->make(Request::class))
                ->with('user', $this->app->make('auth')->user())
                ->with('config', $this->app->make('config'));
        });

        // 构造器
        $this->app->make('view')->creator('*', RequestCreator::class);
        $this->app->make('view')->creator('*', function (View $view) {
            $view->with('request', $this->app->make(Request::class))
                ->with('user', $this->app->make('auth')->user())
                ->with('config', $this->app->make('config'));
        });

        // 共享数据
        $this->app->make('view')->share('request', $this->app->make(Request::class));
        $this->app->make('view')->share('user', $this->app->make('auth')->user());
        $this->app->make('view')->share('config', $this->app->make('config'));

        // 注册组件
        Blade::component('alert', AlertComponent::class);

        /**
         * 扩展 Blade
         *
         * ```blade
         * @datetime($timestamp, $format)
         * ```
         */
        Blade::directive('datetime', function (string $expression) {
            /** @var string[] $args */
            $args = array_slice(explode(',', $expression), 0, 2);

            empty($args[0]) and $args[0] = 'time()';
            empty($args[1]) and $args[1] = "'Y m d H:i:s'";

            $newExpression = implode(', ', array_reverse($args));

            return "<?php echo date($newExpression);?>";
        });

        // 回显变量
        Blade::stringable(function (Request $request) {
            return json_encode($request->all(), JSON_PRETTY_PRINT);
        });
    }
}
