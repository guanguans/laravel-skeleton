<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Providers;

use App\Console\Commands\ClearAllCommand;
use App\Http\Middleware\LogHttp;
use App\Listeners\RunCommandInDebugModeListener;
use App\Listeners\SetRequestIdListener;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Notifications\SlowQueryLoggedNotification;
use App\Policies\UserPolicy;
use App\Support\Attributes\Mixin;
use App\Support\Contracts\ShouldRegisterContract;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\DatabaseBusy;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\DateFactory;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Context;
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
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Lottery;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Laragear\Discover\Facades\Discover;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Laravel\Sanctum\Sanctum;
use Laravel\Telescope\Telescope;
use Opcodes\LogViewer\Facades\LogViewer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

/**
 * @property EventDispatcherInterface $symfonyDispatcher
 */
class AppServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }
    final public const string REQUEST_ID_NAME = 'X-Request-Id';
    public array $bindings = [];
    public array $singletons = [];

    /**
     * Register any application services.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     * @noinspection SensitiveParameterInspection
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    #[\Override]
    public function register(): void
    {
        Scramble::configure()->withDocumentTransformers(static function (OpenApi $openApi): void {
            $openApi->secure(SecurityScheme::http('bearer'));
        });

        $this->whenever(true, function (): void {
            $this->registerGlobalFunctionsFrom($this->app->path('Support/*helpers.php'));
            // \Closure::fromCallable([$this->app->make(SetRequestIdListener::class), 'handle']);
            // $this->booting($this->app->make(SetRequestIdListener::class)->handle(...));
        });

        // foreach (
        //     Finder::create()
        //         ->in(__DIR__)
        //         ->name('*ServiceProvider.php')
        //         ->files() as $file
        // ) {
        //     $class = __NAMESPACE__.'\\'.$file->getBasename('.php');
        //
        //     if (
        //         !is_subclass_of($class, ShouldRegisterContract::class)
        //         || !is_subclass_of($class, ServiceProvider::class)
        //         || AbstractServiceProvider::class === $class
        //     ) {
        //         continue;
        //     }
        //
        //     $provider = new $class($this->app);
        //     $provider->shouldRegister() and $this->app->register($provider);
        // }

        $this->booting(function (): void {
            $this->registerMixins();

            Discover::in('Providers')
                ->instancesOf(ServiceProvider::class)
                ->instancesOf(ShouldRegisterContract::class)
                ->classes()
                ->keys()
                ->each(function (string $class): void {
                    /** @var class-string<ServiceProvider&ShouldRegisterContract> $class */
                    $provider = new $class($this->app);
                    $provider->shouldRegister() and $this->app->register($provider);
                });
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @see https://github.com/cachethq/cachet
     *
     * @noinspection JsonEncodingApiUsageInspection
     *
     * @throws \ReflectionException
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function boot(): void
    {
        // $this->configureRoute();

        $this->whenever(true, function (): void {
            $this->app->instance(self::REQUEST_ID_NAME, (string) Str::uuid());
            \Illuminate\Support\Facades\Request::getFacadeRoot()->headers->set(self::REQUEST_ID_NAME, $this->app->make(self::REQUEST_ID_NAME));
            Context::add('request_id', $this->app->make(self::REQUEST_ID_NAME));

            // // With context for current channel and stack.
            // \Illuminate\Support\Facades\Log::withContext(\\Illuminate\Support\Facades\Request::getFacadeRoot()->headers());

            // if (($logger = \Illuminate\Support\Facades\Log::getLogger()) instanceof \Monolog\Logger) {
            //     $logger->pushProcessor(new AppendExtraDataProcessor(\\Illuminate\Support\Facades\Request::getFacadeRoot()->headers()));
            // }
            $this->preProcessRequest();
        });

        $this->whenever(true, function (): void {
            // ini_set('json.exceptions', '1'); // PHP 8.3
            // 低版本 MySQL(< 5.7.7) 或 MariaDB(< 10.2.2)，则可能需要手动配置迁移生成的默认字符串长度，以便按顺序为它们创建索引。
            Schema::defaultStringLength(191);
            $this->setLocales();
            // @see https://www.php.net/manual/zh/numberformatter.parsecurrency.php
            // @see https://zh.wikipedia.org/wiki/ISO_4217
            Number::useCurrency('CNY');
            // @see \Carbon\Laravel\ServiceProvider
            // Carbon::serializeUsing(static fn (Carbon $timestamp): string => $timestamp->format('Y-m-d H:i:s'));
            Date::use(CarbonImmutable::class);
            DateFactory::useCallable(
                static fn (mixed $result): mixed => $result instanceof CarbonInterface
                    ? $result->setTimezone(Config::string('app.timezone'))
                    : $result
            );
            // @see https://masteringlaravel.io/daily/2024-11-13-how-can-you-make-sure-the-environment-is-configured-correctly
            // env('DB_HOST', fn () => throw new \Exception('DB_HOST is missing'));
            // Env::getOrFail('DB_HOST');
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
            // @see https://www.harrisrafto.eu/simplifying-view-path-management-with-laravels-prependlocation/
            // View::prependLocation(resource_path('custom-views'));
            $this->registerMixins();
            $this->createUrls();
            Password::defaults(
                function (): Password {
                    $password = Password::min(8)->max(255);

                    return $this->app->isProduction()
                        ? $password
                            ->letters()
                            ->mixedCase()
                            ->numbers()
                            ->symbols()
                            ->uncompromised()
                        : $password;
                }
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
                \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_LINE_TERMINATORS
            ));
            LogHttp::skipWhen(fn (Request $request): bool => $this->app->runningUnitTests() || $request->isMethodSafe());
            LogViewer::auth(static fn (): bool => \Illuminate\Support\Facades\Request::getFacadeRoot()::isAdminDeveloper());
            class_exists(Telescope::class) and Telescope::auth(static fn (): bool => \Illuminate\Support\Facades\Request::getFacadeRoot()::isAdminDeveloper());
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

            /** @see https://github.com/AnimeThemes/animethemes-server/blob/main/app/Providers/AppServiceProvider.php */
            EnsureFeaturesAreActive::whenInactive(static fn (Request $request, array $features): Response => new Response(status: 403));
            ClearAllCommand::prohibit(app()->isProduction());
            // Prevents 'migrate:fresh', 'migrate:refresh', 'migrate:reset', and 'db:wipe'
            DB::prohibitDestructiveCommands($this->app->isProduction());

            /** @see https://github.com/OussamaMater/Laravel-Tips#tip-266--the-new-optimizes-method */
            $this->optimizes(
                optimize: 'filament:optimize',
                // Defaults to the service provider name without "ServiceProvider" suffix
                key: 'filament'
            );

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

            /** @var Model $post */
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

            // $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
            // // Add our custom logging middleware after authentication
            // $kernel->addToMiddlewarePriorityAfter(
            //     \Illuminate\Auth\Middleware\Authenticate::class,
            //     [
            //         App\Http\Middleware\LogUserActions::class,
            //         App\Http\Middleware\TrackUserSession::class,
            //     ]
            // );
            // // Add our security checks before any route handling
            // $kernel->addToMiddlewarePriorityBefore(
            //     \Illuminate\Routing\Middleware\SubstituteBindings::class,
            //     [
            //         \App\Http\Middleware\ValidateSecurityHeaders::class,
            //         \App\Http\Middleware\CheckMaintenanceBypass::class,
            //     ]
            // );
        });

        $this->whenever($this->app->runningInConsole(), static function (): void {
            AboutCommand::add('Application', [
                'Name' => 'laravel-skeleton',
                'author' => 'guanguans',
                'github' => 'https://github.com/guanguans/laravel-skeleton',
                'license' => 'MIT License',
            ]);
        });

        $this->whenever(\Illuminate\Support\Facades\Request::getFacadeRoot()?->user()?->locale, function (self $serviceProvider, $locale): void {
            $this->setLocales($locale);
        });

        $this->whenever($this->app->isProduction(), static function (): void {
            // URL::forceHttps();
            // URL::forceScheme('https');
            // $this->app->make(Request::class)->server->set('HTTPS', 'on');
            // $this->app->make(Request::class)->server->set('SERVER_PORT', 443);
            // Config::set('session.secure', true);

            DB::whenQueryingForLongerThan(300000, static function (Connection $connection, QueryExecuted $event): void {
                Notification::send(
                    new AnonymousNotifiable,
                    new SlowQueryLoggedNotification(
                        $event->sql,
                        $event->time,
                        \Illuminate\Support\Facades\Request::getFacadeRoot()->url(),
                    ),
                );
            });

            \Illuminate\Support\Facades\Event::listen(static function (DatabaseBusy $event): void {
                // todo notify
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
            if (!app()->isProduction()) {
                DB::listen(static function (QueryExecuted $query): void {
                    if (250 < $query->time) {
                        Log::warning('An individual database query exceeded 250 ms.', [
                            'sql' => $query->sql,
                            'raw' => $query->toRawSql(),
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
            Model::automaticallyEagerLoadRelationships(); // 避免 N+1 查询问题
            // Model::preventLazyLoading(); // 预防 N+1 查询问题
            // Model::preventSilentlyDiscardingAttributes(); // 防止模型静默丢弃不在 fillable 中的字段
            // Model::preventAccessingMissingAttributes(); // Trigger MissingAttributeException
            // DB::handleExceedingCumulativeQueryDuration();
            // Model::unguard();
            // Model::withoutEvents();
            // DB::listen(static function ($query) {
            //     logger(Str::replaceArray('?', $query->bindings, $query->sql));
            // });
        });

        $this->when($this->isOctaneHttpServer(), function (): void {
            $this->app->get(Dispatcher::class)->listen(RequestReceived::class, static function (): void {
                $uuid = Str::uuid()->toString();

                if (config('octane.server') === 'roadrunner') {
                    Cache::put($uuid, microtime(true));

                    return;
                }

                Cache::store('octane')->put($uuid, microtime(true));
            });

            $this->app->get(Dispatcher::class)->listen(RequestTerminated::class, static function (): void {});
        });
    }

    private function registerGlobalFunctionsFrom(string $pattern, int $flags = 0): void
    {
        foreach (glob($pattern, $flags | \GLOB_BRACE) as $file) {
            require_once $file;
        }
    }

    /**
     * Register macros.
     */
    private function registerMixins(): void
    {
        Discover::in('Support/Mixins')
            ->allClasses()
            ->each(static function (\ReflectionClass $mixinReflectionClass, string $mixinClass): void {
                foreach ($mixinReflectionClass->getAttributes(Mixin::class) as $mixinReflectionAttribute) {
                    /** @var \App\Support\Attributes\Mixin $mixinAttribute */
                    $mixinAttribute = $mixinReflectionAttribute->newInstance();
                    $mixinAttribute->class::mixin(app($mixinClass), $mixinAttribute->replace);
                }
            });
    }

    /**
     * @see https://github.com/nandi95/laravel-starter/blob/main/app/Providers/AppServiceProvider.php
     */
    private function createUrls(): void
    {
        ResetPassword::createUrlUsing(
            static fn (object $notifiable, #[\SensitiveParameter] string $token): string => config('app.frontend_url')."/auth/reset/$token?email={$notifiable->getEmailForPasswordReset()}"
        );

        VerifyEmail::createUrlUsing(static function (object $notifiable): string {
            $url = url()->temporarySignedRoute(
                'email.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'user' => $notifiable->ulid,
                ],
                false
            );

            return config('app.frontend_url').'/auth/verify?verify_url='.urlencode($url);
        });
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

    private function setLocales(?string $locale = null): void
    {
        $locale and $this->app->setLocale($locale);
        Number::useLocale($this->app->getLocale());
        Carbon::setLocale($this->app->getLocale());
    }

    /**
     * Determine if server is running Octane.
     *
     * @noinspection GlobalVariableUsageInspection
     */
    private function isOctaneHttpServer(): bool
    {
        return isset($_SERVER['LARAVEL_OCTANE']) || isset($_ENV['OCTANE_DATABASE_SESSION_TTL']);
    }
}
