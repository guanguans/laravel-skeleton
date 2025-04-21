<?php

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

use App\Notifications\SlowQueryLoggedNotification;
use App\Support\Contracts\ShouldRegisterContract;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\DatabaseBusy;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

class DatabaseServiceProvider extends ServiceProvider implements ShouldRegisterContract
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    public function boot(): void
    {
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
                        Request::getFacadeRoot()->url(),
                    ),
                );
            });

            Event::listen(static function (DatabaseBusy $event): void {
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
    }

    public function shouldRegister(): bool
    {
        return true;
    }
}
