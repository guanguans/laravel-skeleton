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
use App\Policies\UserPolicy;
use App\Support\Attributes\Mixin;
use App\Support\Contracts\ShouldRegisterContract;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\DateFactory;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\View;
use Laragear\Discover\Facades\Discover;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Laravel\Sanctum\Sanctum;
use Laravel\Telescope\Telescope;
use Opcodes\LogViewer\Facades\LogViewer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
        });

        $this->whenever(\Illuminate\Support\Facades\Request::getFacadeRoot()?->user()?->locale, function (self $serviceProvider, $locale): void {
            $this->setLocales($locale);
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
}
