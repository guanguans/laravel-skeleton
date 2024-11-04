<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\LogHttp;
use App\Http\Middleware\TrimStrings;
use App\Listeners\RunCommandInDebugModeListener;
use App\Listeners\SetRequestIdListener;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Notifications\SlowQueryLoggedNotification;
use App\Policies\UserPolicy;
use App\Rules\Rule;
use App\Support\Attributes\After;
use App\Support\Attributes\Before;
use App\Support\Attributes\Injection;
use App\Support\Discover;
use App\Support\Macros\BlueprintMacro;
use App\Support\Macros\CarbonMacro;
use App\Support\Macros\CollectionMacro;
use App\Support\Macros\CommandMacro;
use App\Support\Macros\GrammarMacro;
use App\Support\Macros\ModelMacro;
use App\Support\Macros\MySqlGrammarMacro;
use App\Support\Macros\PendingRequestMacro;
use App\Support\Macros\RequestMacro;
use App\Support\Macros\ResponseFactoryMacro;
use App\Support\Macros\SchedulingEventMacro;
use App\Support\Macros\StringableMacro;
use App\Support\Macros\StrMacro;
use App\Support\Macros\UploadedFileMacro;
use App\View\Components\AlertComponent;
use App\View\Composers\RequestComposer;
use App\View\Creators\RequestCreator;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\DateFactory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Lottery;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Imanghafoori\Decorator\Decorators\DecoratorFactory;
use Imanghafoori\Decorator\Facade\Decorator;
use Jiannei\Response\Laravel\Providers\LaravelServiceProvider;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Sanctum\Sanctum;
use Laravel\Telescope\Telescope;
use Opcodes\LogViewer\Facades\LogViewer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Stillat\BladeDirectives\Support\Facades\Directive;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @property EventDispatcherInterface $symfonyDispatcher
 */
class AppServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    final public const REQUEST_ID_NAME = 'X-Request-Id';

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
    public array $singletons = [];

    /**
     * Register any application services.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register(): void
    {
        $this->whenever(true, function (): void {
            $this->registerGlobalFunctionsFrom($this->app->path('Support/*helpers.php'));
            // $this->app->register(LaravelServiceProvider::class);

            // \Closure::fromCallable([$this->app->make(SetRequestIdListener::class), 'handle']);
            // $this->booting($this->app->make(SetRequestIdListener::class)->handle(...));
        });

        $this->whenever($this->app->isLocal(), function (): void {
            $this->app->register(WhenLocalServiceProvider::class);
        });

        $this->whenever($this->app->runningUnitTests(), function (): void {
            $this->app->register(WhenTestingServiceProvider::class);
        });

        $this->unless($this->app->isProduction(), function (): void {
            $this->app->register(UnlessProductionServiceProvider::class);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     *
     * @see https://github.com/cachethq/cachet
     *
     * @noinspection JsonEncodingApiUsageInspection
     */
    public function boot(): void
    {
        $this->whenever(true, function (): void {
            $this->app->instance(self::REQUEST_ID_NAME, (string) Str::uuid());
            request()->headers->set(self::REQUEST_ID_NAME, $this->app->make(self::REQUEST_ID_NAME));
            Log::shareContext($this->sharedLogContext());

            // // With context for current channel and stack.
            // \Illuminate\Support\Facades\Log::withContext(\request()->headers());

            // if (($logger = \Illuminate\Support\Facades\Log::getLogger()) instanceof \Monolog\Logger) {
            //     $logger->pushProcessor(new AppendExtraDataProcessor(\request()->headers()));
            // }
            $this->preProcessRequest();
        });

        $this->whenever(true, function (): void {
            // ini_set('json.exceptions', '1'); // PHP 8.3
            $this->injection();
            $this->bootAspects();
            $this->bootDecorator();
            // 低版本 MySQL(< 5.7.7) 或 MariaDB(< 10.2.2)，则可能需要手动配置迁移生成的默认字符串长度，以便按顺序为它们创建索引。
            Schema::defaultStringLength(191);
            $this->setLocales();
            // @see https://www.php.net/manual/zh/numberformatter.parsecurrency.php
            // @see https://zh.wikipedia.org/wiki/ISO_4217
            Number::useCurrency('CNY');
            Carbon::serializeUsing(static fn (Carbon $timestamp) => $timestamp->format('Y-m-d H:i:s'));
            Date::use(CarbonImmutable::class);
            DateFactory::useCallable(
                static fn (mixed $result): mixed => $result instanceof CarbonInterface
                    ? $result->setTimezone(config('app.timezone'))
                    : $result
            );
            // JsonResource::wrap('list');
            JsonResource::withoutWrapping();
            ResourceCollection::withoutWrapping();
            // Paginator::useBootstrap();
            // Paginator::useBootstrapFour();
            // Paginator::useBootstrapFive();
            // Paginator::defaultView('pagination::bulma');
            // Paginator::defaultSimpleView('pagination::simple-bulma');
            // Blade::withoutDoubleEncoding(); // 禁用 HTML 实体双重编码
            // Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
            // Sanctum::ignoreMigrations();
            // Gate::policy(User::class, UserPolicy::class);
            // Passport::enablePasswordGrant();
            Builder::defaultMorphKeyType('uuid');
            // Vite::useWaterfallPrefetching(concurrency: 10);
            // Vite::useAggressivePrefetching();
            // Vite::usePrefetchStrategy('waterfall', ['concurrency' => 1]);
            // Vite::useBuildDirectory('.build');
            // Vite::prefetch(4);
            // TrimStrings::except(['secret']);
            // RedirectIfAuthenticated::redirectUsing(static fn ($request) => route('dashboard'));
            $this->registerMacros();
            $this->extendValidator();
            $this->extendView();
            $this->listenEvents();
            Password::defaults(
                static fn (): Password => Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            );
            // Route::middleware(['throttle:uploads']);
            RateLimiter::for(
                'uploads',
                static fn (Request $request) => $request->user()->vipCustomer()
                    ? Limit::none()
                    : Limit::perMinute(100)->by($request->ip())
            );
            ConvertEmptyStringsToNull::skipWhen(static fn (Request $request) => $request->is('api/*'));
            // TrimStrings::skipWhen(static fn (Request $request): bool => $request->is('admin/*'));
            Json::encodeUsing(static fn (mixed $value): bool|string => json_encode(
                $value,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS
            ));
            LogHttp::skipWhen(fn (Request $request): bool => $this->app->runningUnitTests() || $request->isMethodSafe());
            LogViewer::auth(static fn (): bool => request()::isAdminDeveloper());
            class_exists(Telescope::class) and Telescope::auth(static fn (): bool => request()::isAdminDeveloper());
            // $this->app->extend(ExceptionHandler::class, static function (ExceptionHandler $handler, Application $app) {
            //     if (! $handler instanceof \App\Exceptions\Handler) {
            //         // $handler = $app->make(\App\Exceptions\Handler::class);
            //     }
            //
            //     return $handler;
            // });
            Http::globalOptions([
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);
            Http::globalMiddleware(
                Middleware::log(Log::channel('single'), new MessageFormatter(MessageFormatter::DEBUG))
            );
            if (DB::connection() instanceof SQLiteConnection) {
                // Enable on delete cascade for sqlite connections
                DB::statement(DB::raw('PRAGMA foreign_keys = ON')->getValue(DB::getQueryGrammar()));
            }

            // Route::resourceVerbs([
            //     'create' => 'crear',
            //     'edit' => 'editar',
            // ]);

            // Builder::morphUsingUlids();
            // Builder::morphUsingUuids();

            // 自定义多态类型
            Relation::enforceMorphMap([
                'post' => 'App\Models\Post',
                'video' => 'App\Models\Video',
            ]);

            /** @var \App\Models\Post $post */
            // $alias = $post->getMorphClass();
            // $class = Relation::getMorphedModel($alias);
            // Order::resolveRelationUsing('customer', function (Order $order) {
            //     return $order->belongsTo(Customer::class, 'customer_id');
            // });

            // Intercept any Gate and check if it's super admin, Or if you use some permissions package...
            Gate::before(static function ($user, $ability): void {
                // if ($user->is_super_admin == 1) {
                //     return true;
                // }
                //
                // if ($user->hasPermission('root')) {
                //     return true;
                // }
            });

            // $this->app->booted(function (): void {
            //     (fn () => $this->symfonyDispatcher->addListener(
            //         ConsoleEvents::COMMAND,
            //         new RunCommandInDebugModeListener
            //     ))->call($this->app->make(Kernel::class));
            // });
        });

        $this->whenever($this->app->runningInConsole(), static function (): void {
            AboutCommand::add('Application', [
                'Name' => 'laravel-skeleton',
                'author' => 'guanguans',
                'github' => 'https://github.com/guanguans/laravel-skeleton',
                'license' => 'MIT License',
            ]);
        });

        $this->whenever(request()?->user()?->locale, function (self $serviceProvider, $locale): void {
            $this->setLocales($locale);
        });

        $this->whenever($this->app->isProduction(), static function (): void {
            // URL::forceScheme('https');
            // $this->app->make(Request::class)->server->set('HTTPS', 'on');
            // $this->app->make(Request::class)->server->set('SERVER_PORT', 443);

            DB::whenQueryingForLongerThan(300000, static function (Connection $connection, QueryExecuted $event): void {
                Notification::send(
                    new AnonymousNotifiable,
                    new SlowQueryLoggedNotification(
                        $event->sql,
                        $event->time,
                        request()->url(),
                    ),
                );
            });

            /**
             * 此设置将仅报告 1% 的耗时超过 2 秒的查询，从而帮助您监控性能而不会使日志记录系统不堪重负。
             *
             * @see https://www.harrisrafto.eu/harnessing-controlled-randomness-with-laravels-lottery/
             */
            // DB::whenQueryingForLongerThan(
            //     CarbonInterval::seconds(2),
            //     Lottery::odds(1, 100)->winner(static fn () => report('Querying > 2 seconds.')),
            // );

            // Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            //     info(sprintf('Attempted to lazy load [%s] on model [%s].', $relation, get_class($model)));
            // });

            DB::prohibitDestructiveCommands();

            // -----------------------------------------------------------------------
            // LOG-VIEWER : log all queries (not in production)
            // -----------------------------------------------------------------------
            // if (! app()->isProduction()) {
            //     DB::listen(fn ($query) => Log::debug($query->toRawSQL()));
            // }

            // -----------------------------------------------------------------------
            // LOG-VIEWER : log all SLOW queries (not in production)
            // -----------------------------------------------------------------------
            if (! app()->isProduction()) {
                DB::listen(static function (QueryExecuted $query): void {
                    if ($query->time > 250) {
                        Log::warning('An individual database query exceeded 250 ms.', [
                            'sql' => $query->sql,
                            'raw' => $query->toRawSQL(),
                        ]);
                    }
                });
            }
        });

        /** @see \Illuminate\Foundation\Testing\Concerns\InteractsWithTestCaseLifecycle */
        $this->whenever($this->app->environment('testing'), static function (): void {
            // Http::preventStrayRequests(); // Preventing Stray Requests
            // Mail::alwaysTo('taylor@example.com');
            // Carbon::setTestNow('2031-04-05');
            // Carbon::setTestNowAndTimezone('2031-04-05', 'Asia/Shanghai');
            // CarbonImmutable::setTestNow();
            // CarbonImmutable::setTestNowAndTimezone('2031-04-05', 'Asia/Shanghai');
            // ParallelTesting::setUpTestDatabase(static function (string $database, int $token) {
            //     Artisan::call('db:seed');
            // });
        });

        $this->unless($this->app->isProduction(), static function (): void {
            Model::shouldBeStrict(); // Eloquent 严格模式
            // Model::preventLazyLoading(); // 预防 N+1 查询问题
            // Model::preventSilentlyDiscardingAttributes(); // 防止模型静默丢弃不在 fillable 中的字段
            // Model::preventAccessingMissingAttributes(); // Trigger MissingAttributeException
            // DB::handleExceedingCumulativeQueryDuration();
            // Model::unguard();
            // DB::listen(static function ($query) {
            //     logger(Str::replaceArray('?', $query->bindings, $query->sql));
            // });
        });

        $this->when($this->isOctaneHttpServer(), function (): void {
            $this->app->get('events')->listen(RequestReceived::class, static function (): void {
                $uuid = Str::uuid()->toString();

                if (config('octane.server') === 'roadrunner') {
                    Cache::put($uuid, microtime(true));

                    return;
                }

                Cache::store('octane')->put($uuid, microtime(true));
            });

            $this->app->get('events')->listen(RequestTerminated::class, static function (): void {});
        });
    }

    private function registerGlobalFunctionsFrom(string $pattern, int $flags = 0): void
    {
        foreach (glob($pattern, $flags | GLOB_BRACE) as $file) {
            require_once $file;
        }
    }

    /**
     * Register macros.
     *
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    private function registerMacros(): void
    {
        \App\Models\Model::mixin($this->app->make(ModelMacro::class));
        Blueprint::mixin($this->app->make(BlueprintMacro::class));
        Carbon::mixin($this->app->make(CarbonMacro::class));
        Collection::mixin($this->app->make(CollectionMacro::class));
        Command::mixin($this->app->make(CommandMacro::class));
        Event::mixin($this->app->make(SchedulingEventMacro::class));
        Grammar::mixin($this->app->make(GrammarMacro::class));
        MySqlGrammar::mixin($this->app->make(MySqlGrammarMacro::class));
        PendingRequest::mixin($this->app->make(PendingRequestMacro::class));
        Request::mixin($this->app->make(RequestMacro::class));
        ResponseFactory::mixin($this->app->make(ResponseFactoryMacro::class));
        Str::mixin($this->app->make(StrMacro::class));
        Stringable::mixin($this->app->make(StringableMacro::class));
        UploadedFile::mixin($this->app->make(UploadedFileMacro::class));

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
    private function extendValidator(): void
    {
        Discover::in('Rules')
            ->instanceOf(Rule::class)
            ->all()
            ->each(static function (\ReflectionClass $ruleReflectionClass, $ruleClass): void {
                /** @var Rule&class-string $ruleClass */
                Validator::{$ruleClass::extendMethod()}(
                    $ruleClass::name(),
                    static fn (
                        string $attribute,
                        mixed $value,
                        array $parameters,
                        \Illuminate\Validation\Validator $validator
                    ): bool => tap(new $ruleClass(...$parameters), static function (Rule $rule) use ($validator): void {
                        $rule instanceof ValidatorAwareRule and $rule->setValidator($validator);
                        $rule instanceof DataAwareRule and $rule->setData($validator->getData());
                    })->passes($attribute, $value),
                    $ruleClass::message()
                );
            });
    }

    /**
     * @throws BindingResolutionException
     */
    private function extendView(): void
    {
        /** @see https://www.harrisrafto.eu/simplifying-view-logic-with-laravel-blades-service-injection */
        // resources/views/dashboard.blade.php
        // @inject('metrics', 'App\Services\DashboardMetricsService');

        /** @see https://www.harrisrafto.eu/enhancing-frontend-interactivity-with-laravel-blade-fragments */
        // return view('dashboard', ['users' => $users])->fragment('user-list');

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
        Blade::directive('datetime', static function (string $expression): string {
            // 通用解析表达式
            $parts = value(static function (string $expression): array {
                // clean
                $parts = array_map(trim(...), explode(',', Blade::stripParentheses($expression)));
                // filter
                $parts = array_filter($parts, static fn (string $part): bool => '' !== $part);

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
        Blade::if('disk', static fn ($value): bool => config('filesystems.default') === $value);

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
        ): string => '<?php echo '.\Illuminate\Support\Str::class.'::slug($title, $separator, $language, $dictionary); ?>');
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function preProcessRequest(): void
    {
        $request = $this->app->make(Request::class);

        $request->headers->set(self::REQUEST_ID_NAME, $this->app->make(self::REQUEST_ID_NAME));

        if ($request->is('api/v1/*')) {
            $request->headers->set('Accept', 'application/json');
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @noinspection ForgottenDebugOutputInspection
     */
    private function listenEvents(): void
    {
        // $this->app->get('events')->listen(StatementPrepared::class, static function (StatementPrepared $event): void {
        //     $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
        // });

        // $this->app->get('events')->listen(DatabaseBusy::class, static function (DatabaseBusy $event) {
        //     Notification::route('mail', 'dev@example.com')
        //         ->notify(new DatabaseApproachingMaxConnections(
        //             $event->connectionName,
        //             $event->connections
        //         ));
        // });

        $this->app->get('events')->listen(RequestHandled::class, static function (RequestHandled $event): void {
            if ($event->response instanceof JsonResponse) {
                $event->response->setEncodingOptions($event->response->getEncodingOptions() | JSON_UNESCAPED_UNICODE);
            }
        });

        // \Illuminate\Support\Facades\Event::listen('*', static function (string $event, array $data): void {
        //     // Log the event class
        //     error_log($event);
        //     // Log the event data delegated to listener parameters
        //     error_log(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS));
        // });
    }

    private function setLocales(?string $locale = null): void
    {
        $locale and $this->app->setLocale($locale);
        Number::useLocale($this->app->getLocale());
        Carbon::setLocale($this->app->getLocale());
    }

    /**
     * @throws BindingResolutionException
     */
    private function sharedLogContext(): array
    {
        return collect([
            'php-version' => PHP_VERSION,
            'php-interface' => \PHP_SAPI,
            'laravel-version' => $this->app->version(),
            'running-in-console' => $this->app->runningInConsole(),
            self::REQUEST_ID_NAME => $this->app->make(self::REQUEST_ID_NAME),
        ])->unless(
            $this->app->runningInConsole(),
            static fn (Collection $context): Collection => $context->merge([
                'user-id' => request()->user()?->id,
                'url' => request()->url(),
                'ip' => request()->ip(),
                'method' => request()->method(),
                // 'action' => request()->route()?->getActionName(),
            ])
        )->all();
    }

    /**
     * @noinspection PhpExpressionResultUnusedInspection
     * @noinspection VirtualTypeCheckInspection
     */
    private function injection(): void
    {
        $this->app->resolving(static function (mixed $object, Application $app): void {
            if (
                ! \is_object($object)
                || ! str($object::class)->is(config('services.injection.only'))
                || str($object::class)->is(config('services.injection.except'))
            ) {
                return;
            }

            $reflectionObject = new \ReflectionObject($object);

            foreach ($reflectionObject->getProperties() as $reflectionProperty) {
                if (
                    ! $reflectionProperty->isDefault()
                    || $reflectionProperty->isStatic()
                    || [] === ($attributes = $reflectionProperty->getAttributes(Injection::class))
                ) {
                    continue;
                }

                /** @var Injection $injection */
                $injection = $attributes[0]->newInstance();

                $property = "{$reflectionObject->getName()}::\${$reflectionProperty->getName()}";

                $propertyType = value(static function () use ($injection, $reflectionProperty, $property): string {
                    if ($injection->propertyType) {
                        return $injection->propertyType;
                    }

                    $reflectionPropertyType = $reflectionProperty->getType();
                    if ($reflectionPropertyType instanceof \ReflectionNamedType && ! $reflectionPropertyType->isBuiltin()) {
                        return $reflectionPropertyType->getName();
                    }

                    throw new \LogicException(\sprintf(
                        "Attribute [%s] of property [$property] miss a argument [propertyType], or property [$property] mustn't be a built-in named type.",
                        Injection::class,
                    ));
                });

                try {
                    $reflectionProperty->isPublic() or $reflectionProperty->setAccessible(true);
                    $reflectionProperty->setValue($object, $app->make($propertyType, $injection->parameters));
                } catch (\Throwable $throwable) {
                    throw new \TypeError(
                        "Type [$propertyType] of property [$property] resolve failed [{$throwable->getMessage()}].",
                        $throwable->getCode(),
                        $throwable
                    );
                }
            }
        });
    }

    private function bootAspects(): void
    {
        $classes = \Spatie\StructureDiscoverer\Discover::in(app_path())
            ->classes()
            ->custom(
                static fn (
                    DiscoveredClass $discoveredClass
                ): bool => ! $discoveredClass->isAbstract && ! Str::endsWith($discoveredClass->name, ['(', '{', 'Controller'])
            )
            ->get();

        collect($classes)->filter(class_exists(...))->each(function (string $class): void {
            $reflectionClass = new \ReflectionClass($class);

            $reflectionMethods = ($reflectionClass)->getMethods();

            $condition = Arr::first(
                $reflectionMethods,
                static fn (
                    \ReflectionMethod $reflection
                ): bool => $reflection->getAttributes(Before::class) || $reflection->getAttributes(After::class)
            );

            if ($condition) {
                $this->app->extend($class, static fn (object $object): object => new class($object, $reflectionMethods)
                {
                    public function __construct(
                        private readonly object $object,
                        private readonly array $reflectionMethods,
                    ) {}

                    /**
                     * @noinspection MissingReturnTypeInspection
                     */
                    public function __call(string $name, array $arguments)
                    {
                        if (method_exists($this->object, $name)) {
                            $this->applyAttribute(Before::class, $name, $arguments);

                            $ret = $this->object->{$name}(...$arguments);

                            $this->applyAttribute(After::class, $name, [$ret, ...$arguments]);

                            return $ret;
                        }

                        throw new \BadMethodCallException(
                            \sprintf('The method [%s::%s()] does not exist.', $this->object::class, $name),
                        );
                    }

                    public function __get(string $name): mixed
                    {
                        return $this->object->{$name};
                    }

                    public function __set(string $name, mixed $value): void
                    {
                        $this->object->{$name} = $value;
                    }

                    public function __isset(string $name): bool
                    {
                        return isset($this->object->{$name});
                    }

                    public function __unset(string $name): void
                    {
                        unset($this->object->{$name});
                    }

                    private function applyAttribute(string $attribute, string $name, array $arguments): void
                    {
                        $reflectionAttributes = Arr::first(
                            $this->reflectionMethods,
                            static fn (\ReflectionMethod $reflectionMethod): bool => $reflectionMethod->getName() === $name
                        )->getAttributes($attribute);

                        foreach ($reflectionAttributes as $reflectionAttribute) {
                            app()->call($reflectionAttribute->newInstance()->callback, $arguments);
                        }
                    }
                });
            }
        });
    }

    private function bootDecorator(): void
    {
        Decorator::decorate(
            'UserRepository@find',
            DecoratorFactory::cache(static fn ($madId): string => 'mad_user_key_'.$madId, 10)
        );
    }

    /**
     * Determine if server is running Octane
     *
     * @noinspection GlobalVariableUsageInspection
     */
    private function isOctaneHttpServer(): bool
    {
        return isset($_SERVER['LARAVEL_OCTANE']) || isset($_ENV['OCTANE_DATABASE_SESSION_TTL']);
    }
}
