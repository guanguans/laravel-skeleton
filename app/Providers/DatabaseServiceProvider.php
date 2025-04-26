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

use App\Models\Province;
use App\Models\User;
use App\Notifications\SlowQueryLoggedNotification;
use Carbon\CarbonInterval;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\DatabaseBusy;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Lottery;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Conditionable;

final class DatabaseServiceProvider extends ServiceProvider
{
    use Conditionable {
        Conditionable::when as whenever;
    }

    /**
     * @throws \JsonException
     */
    public function boot(): void
    {
        $this->ever();
        $this->never();
    }

    /**
     * @throws \JsonException
     */
    private function ever(): void
    {
        $this->whenever(true, function (): void {
            $this->whenProduction();
            $this->whenSQLiteConnection();
            $this->unlessProduction();
            $this->listenQueryExecuted();
            Json::encodeUsing(static fn (mixed $value): bool|string => json_encode(
                $value,
                \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_LINE_TERMINATORS
            ));
        });
    }

    private function never(): void
    {
        $this->whenever(false, static function (): void {
            /**
             * 低版本 MySQL(< 5.7.7) 或 MariaDB(< 10.2.2)，则可能需要手动配置迁移生成的默认字符串长度，以便按顺序为它们创建索引。
             */
            Schema::defaultStringLength(191);

            Builder::defaultMorphKeyType('uuid');
            Builder::morphUsingUlids();
            Builder::morphUsingUuids();
            Model::unguard();
            Model::withoutEvents(static function (): void {});

            Model::handleLazyLoadingViolationUsing(static function (Model $model, string $relation): void {
                info(\sprintf('Attempted to lazy load [%s] on model [%s].', $relation, $model::class));
            });

            /**
             * 自定义多态类型.
             */
            Relation::enforceMorphMap([
                'post' => 'App\Models\Post',
                'video' => 'App\Models\Video',
            ]);

            // /**
            //  * @var Model $post
            //  */
            // $alias = $post->getMorphClass();
            // $class = Relation::getMorphedModel($alias);

            User::resolveRelationUsing(
                'province',
                static fn (User $user) => $user->belongsTo(Province::class, 'province_id')
            );

            Event::listen(StatementPrepared::class, static function (StatementPrepared $event): void {
                $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
            });
        });
    }

    private function whenProduction(): void
    {
        $this->whenever($this->app->isProduction(), static function (): void {
            DB::prohibitDestructiveCommands();

            /**
             * 此设置将仅报告 1% 的耗时超过 2 秒的查询，从而帮助您监控性能而不会使日志记录系统不堪重负。
             *
             * @see https://www.harrisrafto.eu/harnessing-controlled-randomness-with-laravels-lottery/
             */
            DB::whenQueryingForLongerThan(
                CarbonInterval::seconds(2),
                Lottery::odds(1, 100)->winner(static fn () => report('Querying > 2 seconds.')),
            );

            DB::whenQueryingForLongerThan(
                300000,
                static function (Connection $connection, QueryExecuted $queryExecuted): void {
                    Notification::send(
                        new AnonymousNotifiable,
                        new SlowQueryLoggedNotification(
                            $queryExecuted->toRawSql(),
                            $queryExecuted->time,
                            Request::url(),
                        ),
                    );
                }
            );

            Event::listen(static function (DatabaseBusy $event): void {});
        });
    }

    private function unlessProduction(): void
    {
        $this->unless($this->app->isProduction(), static function (): void {
            Model::automaticallyEagerLoadRelationships(); // 避免 N+1 查询问题
            Model::preventAccessingMissingAttributes(); // Trigger MissingAttributeException
            Model::preventLazyLoading(); // 预防 N+1 查询问题
            Model::preventSilentlyDiscardingAttributes(); // 防止模型静默丢弃不在 fillable 中的字段
            Model::shouldBeStrict(); // Eloquent 严格模式
        });
    }

    private function whenSQLiteConnection(): void
    {
        $this->whenever(DB::connection() instanceof SQLiteConnection, static function (): void {
            /**
             * Enable on delete cascade for sqlite connections.
             */
            DB::statement(DB::raw('PRAGMA foreign_keys = ON')->getValue(DB::getQueryGrammar()));
        });
    }

    /**
     * @see https://github.com/overtrue/laravel-query-logger
     */
    private function listenQueryExecuted(): void
    {
        if (!Config::get('logging.query.enabled', false)) {
            return;
        }

        $requestHasTrigger = static fn (string $trigger): bool => false !== getenv($trigger)
            || Request::hasHeader($trigger)
            || Request::has($trigger)
            || Request::hasCookie($trigger);

        if (!empty($trigger = Config::get('logging.query.trigger')) && !$requestHasTrigger($trigger)) {
            return;
        }

        Event::listen(QueryExecuted::class, static function (QueryExecuted $query): void {
            if (
                Config::get('logging.query.slower_than', 0) > $query->time
                || str($query->sql)->is(Config::get('logging.query.except', []))
            ) {
                return;
            }

            Log::channel(config('logging.query.channel'))->debug($query->toRawSql(), [
                'time' => $query->time,
                'humanly_time' => humans_milliseconds($query->time),
                'connection_name' => $query->connectionName,
                // 'database_name' => $query->connection->getDatabaseName(),
                // 'driver_name' => $query->connection->getDriverName(),
            ]);
        });
    }
}
