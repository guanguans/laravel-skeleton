<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\LogHttp;
use App\Macros\BlueprintMacro;
use App\Macros\CollectionMacro;
use App\Macros\CommandMacro;
use App\Macros\GrammarMacro;
use App\Macros\MySqlGrammarMacro;
use App\Macros\RequestMacro;
use App\Macros\ResponseFactoryMacro;
use App\Macros\StringableMacro;
use App\Macros\StrMacro;
use App\Rules\Rule;
use App\Support\Discover;
use App\View\Components\AlertComponent;
use App\View\Composers\RequestComposer;
use App\View\Creators\RequestCreator;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\View;
use NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Reliese\Coders\CodersServiceProvider;
use Stillat\BladeDirectives\Support\Facades\Directive;

class AppServiceProvider extends ServiceProvider
{
    use Conditionable {
        when as whenever;
    }

    /**
     * All of the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public array $bindings = [];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array<array-key, string>
     */
    public array $singletons = [
        BlueprintMacro::class,
        CollectionMacro::class,
        CommandMacro::class,
        GrammarMacro::class,
        MySqlGrammarMacro::class,
        RequestMacro::class,
        ResponseFactoryMacro::class,
        StringableMacro::class,
        StrMacro::class,
    ];

    /**
     * Register any application services.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function register(): void
    {
        $this->whenever(true, function (): void {
            $this->registerGlobalFunctionsFrom($this->app->path('Support/*helpers.php'));
        });

        $this->unless($this->app->isProduction(), function (): void {
            $this->registerNotProductionServiceProviders();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function boot(): void
    {
        $this->whenever(true, function (): void {
            // 低版本 MySQL(< 5.7.7) 或 MariaDB(< 10.2.2)，则可能需要手动配置迁移生成的默认字符串长度，以便按顺序为它们创建索引。
            Schema::defaultStringLength(191);
            $this->app->setLocale($locale = config('app.locale'));
            Carbon::setLocale($locale);
            Carbon::serializeUsing(static fn (Carbon $timestamp) => $timestamp->format('Y-m-d H:i:s'));
            // JsonResource::wrap('data');
            JsonResource::withoutWrapping();
            Paginator::useBootstrap();
            // Blade::withoutDoubleEncoding(); // 禁用 HTML 实体双重编码
            $this->registerMacros();
            $this->extendValidator();
            $this->extendView();
            $this->listenEvents();
            ConvertEmptyStringsToNull::skipWhen(static fn (Request $request) => $request->is('api/*'));
            LogHttp::skipWhen(fn () => $this->app->runningUnitTests());
        });

        $this->whenever(request()?->user()?->locale, static function (self $serviceProvider, $locale): void {
            $serviceProvider->app->setLocale($locale);
        });

        $this->whenever($this->app->isProduction(), static function (): void {
            // URL::forceScheme('https');
            // $this->app->make(Request::class)->server->set('HTTPS', 'on');
            // $this->app->make(Request::class)->server->set('SERVER_PORT', 443);
        });

        $this->whenever($this->app->environment('testing'), static function (): void {
            // Http::preventStrayRequests(); // Preventing Stray Requests
        });

        $this->unless($this->app->isProduction(), static function (): void {
            Model::shouldBeStrict(); // Eloquent 严格模式
            // Model::preventLazyLoading(); // 预防 N+1 查询问题
            // Model::preventSilentlyDiscardingAttributes(); // 防止模型静默丢弃不在 fillable 中的字段
            // Model::preventsAccessingMissingAttributes(); // Triggers MissingAttributeException
            // DB::handleExceedingCumulativeQueryDuration();
        });
    }

    protected function registerGlobalFunctionsFrom(string $pattern): void
    {
        $files = glob($pattern);
        foreach ($files as $file) {
            require_once $file;
        }
    }

    protected function registerNotProductionServiceProviders(): void
    {
        $this->app->register(CollisionServiceProvider::class);
        $this->app->register(IdeHelperServiceProvider::class);
        $this->app->register(\Lanin\Laravel\ApiDebugger\ServiceProvider::class);
        $this->app->register(CodersServiceProvider::class);
    }

    /**
     * Register macros.
     *
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    protected function registerMacros(): void
    {
        Blueprint::mixin($this->app->make(BlueprintMacro::class));
        Collection::mixin($this->app->make(CollectionMacro::class));
        Command::mixin($this->app->make(CommandMacro::class));
        Grammar::mixin($this->app->make(GrammarMacro::class));
        MySqlGrammar::mixin($this->app->make(MySqlGrammarMacro::class));
        Request::mixin($this->app->make(RequestMacro::class));
        ResponseFactory::mixin($this->app->make(ResponseFactoryMacro::class));
        Stringable::mixin($this->app->make(StringableMacro::class));
        Str::mixin($this->app->make(StrMacro::class));

        collect(glob($this->app->path('Macros/QueryBuilder/*QueryBuilderMacro.php')))
            ->each(function ($file): void {
                $queryBuilderMacro = $this->app->make(resolve_class_from($file));
                QueryBuilder::mixin($queryBuilderMacro);
                EloquentBuilder::mixin($queryBuilderMacro);
                Relation::mixin($queryBuilderMacro);
            });
    }

    /**
     * Register rule.
     */
    protected function extendValidator(): void
    {
        Discover::in('Rules')
            ->instanceOf(Rule::class)
            ->all()
            ->each(static function (\ReflectionClass $ruleReflectionClass, $ruleClass): void {
                Validator::{is_subclass_of($ruleClass, ImplicitRule::class) ? 'extendImplicit' : 'extend'}(
                    $ruleClass::name(),
                    static function (string $attribute, $value, array $parameters, \Illuminate\Validation\Validator $validator) use ($ruleClass) {
                        return tap(new $ruleClass(...$parameters), static function (Rule $rule) use ($validator): void {
                            $rule instanceof ValidatorAwareRule and $rule->setValidator($validator);
                            $rule instanceof DataAwareRule and $rule->setData($validator->getData());
                        })->passes($attribute, $value);
                    },
                    $ruleClass::localizedMessage()
                );
            });
    }

    /**
     * @throws BindingResolutionException
     */
    protected function extendView(): void
    {
        // 合成器
        $this->app->make('view')->composer('*', RequestComposer::class);
        $this->app->make('view')->composer('*', function (View $view): void {
            $view->with('request', $this->app->make(Request::class))
                ->with('user', $this->app->make('auth')->user())
                ->with('config', $this->app->make('config'));
        });

        // 构造器
        $this->app->make('view')->creator('*', RequestCreator::class);
        $this->app->make('view')->creator('*', function (View $view): void {
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

        /*
         * 扩展 Blade
         *
         * ```blade
         *
         * @datetime($timestamp, $format)
         * ```
         */
        Blade::directive('datetime', static function (string $expression) {
            // 通用解析表达式
            $parts = value(static function (string $expression): array {
                // clean
                $parts = array_map(trim(...), explode(',', Blade::stripParentheses($expression)));
                // filter
                $parts = array_filter($parts, static fn (string $part) => '' !== $part);
                // default
                return $parts + ['time()', "'Y m d H:i:s'"];
            }, $expression);

            $newExpression = implode(', ', array_reverse($parts));

            return "<?php echo date($newExpression);?>";
        });

        /*
         * 自定义 if 声明
         *
         * ```blade
         *
         * @disk('local')
         *     <! --应用正在使用 local 存储...-->
         *
         * @elsedisk('s3')
         *     <! --应用正在使用 s3 存储...-->
         *
         * @else
         *     <! --应用正在使用其他存储...-->
         *
         * @enddisk
         *
         * @unlessdisk('local')
         *     < ! --应用当前没有使用 local 存储...-->
         *
         * @enddisk
         * ```
         */
        Blade::if('disk', static fn ($value) => config('filesystems.default') === $value);

        // 回显变量
        Blade::stringable(static fn (Request $request) => json_encode(
            $request->all(),
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
        ));

        Directive::callback('limit', static fn ($value, $limit = 100, $end = '...') => Str::limit(
            $value,
            $limit,
            $end
        ));

        Directive::compile('slugify', static fn (
            $title,
            $separator = '-',
            $language = 'en',
            $dictionary = ['@' => 'at']
        ) => '<?php echo \Illuminate\Support\Str::slug($title, $separator, $language, $dictionary); ?>');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function listenEvents(): void
    {
        $this->app->get('events')->listen(StatementPrepared::class, static function (StatementPrepared $event): void {
            $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
        });
    }
}
