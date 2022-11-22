<?php

namespace App\Providers;

use App\Macros\BlueprintMacro;
use App\Macros\CollectionMacro;
use App\Macros\CommandMacro;
use App\Macros\GrammarMacro;
use App\Macros\MySqlGrammarMacro;
use App\Macros\QueryBuilderMacro;
use App\Macros\RequestMacro;
use App\Macros\StringableMacro;
use App\Macros\StrMacro;
use App\Macros\WhereNotQueryBuilderMacro;
use App\Rules\BetweenWordsRule;
use App\Rules\DefaultRule;
use App\Rules\ImplicitRule;
use App\Rules\InstanceofRule;
use App\Rules\Rule;
use App\Traits\Conditionable;
use App\View\Components\AlertComponent;
use App\View\Composers\RequestComposer;
use App\View\Creators\RequestCreator;
use ArgumentCountError;
use Illuminate\Console\Command;
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
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

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
        \App\Macros\RequestMacro::class,
        \App\Macros\CollectionMacro::class,
        \App\Macros\StrMacro::class,
        \App\Macros\StringableMacro::class,
        \App\Macros\QueryBuilderMacro::class,
        \App\Macros\BlueprintMacro::class,
        \App\Macros\GrammarMacro::class,
        \App\Macros\MySqlGrammarMacro::class,
        \App\Macros\CommandMacro::class,
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
            Model::shouldBeStrict(); // Eloquent 严格模式
            // Model::preventLazyLoading(); // 预防 N+1 查询问题
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
        QueryBuilder::mixin($whereNotQueryBuilderMacro = $this->app->make(WhereNotQueryBuilderMacro::class));
        EloquentBuilder::mixin($queryBuilderMacro);
        EloquentBuilder::mixin($whereNotQueryBuilderMacro);
        Relation::mixin($queryBuilderMacro);
        Relation::mixin($whereNotQueryBuilderMacro);
        Str::mixin($this->app->make(StrMacro::class));
        Stringable::mixin($this->app->make(StringableMacro::class));
        Blueprint::mixin($this->app->make(BlueprintMacro::class));
        Grammar::mixin($this->app->make(GrammarMacro::class));
        MySqlGrammar::mixin($this->app->make(MySqlGrammarMacro::class));
        Command::mixin($this->app->make(CommandMacro::class));
    }

    protected function extendValidator(): void
    {
        // 默认值规则
        Validator::extendImplicit('default', function (string $attribute, $value, array $parameters, \Illuminate\Validation\Validator $validator) {
            return (new DefaultRule($parameters[0] ?? $value))
                ->setValidator($validator)
                ->passes($attribute, $value);
        });

        foreach (
            [
                BetweenWordsRule::class,
                InstanceofRule::class,
            ] as $classOfRule
        ) {
            $this->ruleRegistrar($classOfRule);
        }
    }

    /**
     * Register rule.
     */
    protected function extendValidatorFrom($dirs, $name = '*Rule.php', $notName = [])
    {
        foreach (Finder::create()->files()->name($name)->notName($notName)->in($dirs) as $splFileInfo) {
            $classOfRule = transform($splFileInfo, function (SplFileInfo $splFileInfo) {
                $class = trim(Str::replaceFirst(base_path(), '', $splFileInfo->getRealPath()), DIRECTORY_SEPARATOR);

                return str_replace(
                    [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())).'\\'],
                    ['\\', app()->getNamespace()],
                    ucfirst(Str::replaceLast('.php', '', $class))
                );
            });

            try {
                /** @var \App\Rules\Rule $rule */
                $rule = app($classOfRule);
            } catch (Throwable $e) {
                continue;
            }

            if (! $rule instanceof Rule || $rule instanceof DataAwareRule || $rule instanceof ValidatorAwareRule) {
                continue;
            }

            $methodOfExtend = 'extend';
            $rule instanceof ImplicitRule and $methodOfExtend = 'extendImplicit';
            Validator::$methodOfExtend($rule->getName(), "$classOfRule@passes", $rule->message());
        }
    }

    protected function ruleRegistrar(string $classOfRule): void
    {
        $name = Str::of(class_basename($classOfRule))->replaceLast('Rule', '')->snake()->__toString();

        Validator::extend($name, function (string $attribute, $value, array $parameters, \Illuminate\Validation\Validator $validator) use ($classOfRule) {
            $numberOfRequiredParameters = value(function (string $class): int {
                $constructor = (new ReflectionClass($class))->getConstructor();
                if (is_null($constructor)) {
                    return 0;
                }

                $parametersOfConstructor = $constructor->getParameters();
                $numberOfRequiredParameters = 0;
                foreach ($parametersOfConstructor as $parameter) {
                    if ($parameter->isDefaultValueAvailable()) {
                        break;
                    }

                    $numberOfRequiredParameters++;
                }

                return $numberOfRequiredParameters;
            }, $classOfRule);

            $numberOfIncoming = count($parameters);
            if ($numberOfIncoming !== $numberOfRequiredParameters) {
                throw new ArgumentCountError(
                    sprintf(
                        'Too few arguments to function %s::__construct(), %s passed in %s on line %s and exactly %s expected',
                        $classOfRule,
                        $numberOfIncoming,
                        __FILE__,
                        __LINE__,
                        $numberOfRequiredParameters
                    )
                );
            }

            return (new $classOfRule(...$parameters))->passes($attribute, $value);
        });
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
         *
         * @datetime($timestamp, $format)
         * ```
         */
        Blade::directive('datetime', function (string $expression) {
            // 通用解析表达式
            $parts = value(function (string $expression): array {
                // clean
                $parts = array_map(function (string $part) {
                    return trim($part);
                }, explode(',', Blade::stripParentheses($expression)));

                // filter
                $parts = array_filter($parts, function (string $part) {
                    return $part !== '';
                });

                // default
                return $parts + [
                    0 => 'time()',
                    1 => "'Y m d H:i:s'",
                ];
            }, $expression);

            $newExpression = implode(', ', array_reverse($parts));

            return "<?php echo date($newExpression);?>";
        });

        // 回显变量
        Blade::stringable(function (Request $request) {
            return json_encode($request->all(), JSON_PRETTY_PRINT);
        });
    }
}
