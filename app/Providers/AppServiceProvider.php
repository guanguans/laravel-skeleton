<?php

namespace App\Providers;

use App\Macros\BlueprintMacro;
use App\Macros\CollectionMacro;
use App\Macros\CommandMacro;
use App\Macros\GrammarMacro;
use App\Macros\MySqlGrammarMacro;
use App\Macros\RequestMacro;
use App\Macros\StringableMacro;
use App\Macros\StrMacro;
use App\Rules\Rule;
use App\Traits\Conditionable;
use App\View\Components\AlertComponent;
use App\View\Composers\RequestComposer;
use App\View\Creators\RequestCreator;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\View\View;
use NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider;
use ReflectionClass;
use Reliese\Coders\CodersServiceProvider;
use Symfony\Component\Finder\Finder;

class AppServiceProvider extends ServiceProvider
{
    use Conditionable;

    /**
     * All of the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public $bindings = [];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array<array-key, string>
     */
    public $singletons = [
        BlueprintMacro::class,
        CollectionMacro::class,
        CommandMacro::class,
        GrammarMacro::class,
        MySqlGrammarMacro::class,
        RequestMacro::class,
        StringableMacro::class,
        StrMacro::class,
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
            // JsonResource::wrap('data');
            JsonResource::withoutWrapping();
            Paginator::useBootstrap();
            // Blade::withoutDoubleEncoding(); // 禁用 HTML 实体双重编码
            $this->registerMacros();
            $this->extendValidatorFrom($this->app->path('Rules'));
            $this->extendView();
            ConvertEmptyStringsToNull::skipWhen(function (Request $request) {
                return $request->is('api/*');
            });
        });

        $this->while(\request()?->user()?->locale, static function (self $serviceProvider, $locale) {
            $serviceProvider->app->setLocale($locale);
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
        $this->app->register(CollisionServiceProvider::class);
        $this->app->register(IdeHelperServiceProvider::class);
        $this->app->register(\Lanin\Laravel\ApiDebugger\ServiceProvider::class);
        $this->app->register(CodersServiceProvider::class);
    }

    /**
     * Register macros.
     */
    protected function registerMacros()
    {
        Blueprint::mixin($this->app->make(BlueprintMacro::class));
        Collection::mixin($this->app->make(CollectionMacro::class));
        Command::mixin($this->app->make(CommandMacro::class));
        Grammar::mixin($this->app->make(GrammarMacro::class));
        MySqlGrammar::mixin($this->app->make(MySqlGrammarMacro::class));
        Request::mixin($this->app->make(RequestMacro::class));
        Stringable::mixin($this->app->make(StringableMacro::class));
        Str::mixin($this->app->make(StrMacro::class));

        $files = glob($this->app->path('Macros/QueryBuilder/*QueryBuilderMacro.php'));
        foreach ($files as $file) {
            QueryBuilder::mixin($queryBuilderMacro = $this->app->make(resolve_class_from_path($file)));
            EloquentBuilder::mixin($queryBuilderMacro);
            Relation::mixin($queryBuilderMacro);
        }
    }

    /**
     * Register rule.
     */
    protected function extendValidatorFrom(string|array $dirs, string|array $name = '*Rule.php', string|array $notName = [])
    {
        foreach (Finder::create()->files()->name($name)->notName($notName)->in($dirs) as $splFileInfo) {
            $class = resolve_class_from_path($splFileInfo->getRealPath());
            if (! is_subclass_of($class, Rule::class)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($class);
            if (! $reflectionClass->isInstantiable()) {
                continue;
            }

            $methodOfExtend = transform($class, function (string $classOfRule) {
                $method = 'extend';
                if (is_subclass_of($classOfRule, ImplicitRule::class)) {
                    $method = 'extendImplicit';
                }

                return $method;
            });

            // 有构造函数
            if ($reflectionClass->getConstructor() && $reflectionClass->getConstructor()->getNumberOfParameters()) {
                Validator::$methodOfExtend(
                    $class::name(),
                    function (string $attribute, $value, array $parameters, \Illuminate\Validation\Validator $validator) use ($class) {
                        return tap((new $class(...$parameters)), function (Rule $rule) use ($validator) {
                            $rule instanceof ValidatorAwareRule and $rule->setValidator($validator);
                            $rule instanceof DataAwareRule and $rule->setData($validator->getData());
                        })->passes($attribute, $value);
                    },
                    $class::localizedMessage()
                );

                continue;
            }

            Validator::$methodOfExtend($class::name(), "$class@passes", $class::localizedMessage());
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

        /**
         * 自定义 if 声明
         *
         * ```blade
         *
         * @disk('local')
         *     <! --应用正在使用 local 存储...-->
         * @elsedisk('s3')
         *     <! --应用正在使用 s3 存储...-->
         * @else
         *     <! --应用正在使用其他存储...-->
         * @enddisk
         *
         * @unlessdisk('local')
         *     < ! --应用当前没有使用 local 存储...-->
         * @enddisk
         * ```
         */
        Blade::if('disk', function ($value) {
            return config('filesystems.default') === $value;
        });

        // 回显变量
        Blade::stringable(function (Request $request) {
            return json_encode($request->all(), JSON_PRETTY_PRINT);
        });
    }
}
