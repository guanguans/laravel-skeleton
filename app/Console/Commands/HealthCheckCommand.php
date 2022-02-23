<?php

namespace App\Console\Commands;

use App\Enums\HealthCheckStateEnum;
use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ReflectionMethod;
use ReflectionObject;
use Throwable;

class HealthCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Health check.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        collect((new ReflectionObject($this))->getMethods(ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE))
            ->filter(function (ReflectionMethod $method) {
                return Str::of($method->name)->startsWith('check');
            })
            ->map(function (ReflectionMethod $method) {
                /* @var HealthCheckStateEnum $state */
                $state = call_user_func([$this, $method->name]);

                return [
                    'resource' => Str::of($method->name)->replaceFirst('check', '')->__toString(),
                    'state' => $state->value,
                    'message' => $state->description,
                ];
            })
            ->pipe(function (Collection $checks) {
                $this->table(['Resource', 'State', 'Message'], $checks);
            });

        return 0;
    }

    /**
     * @param $connection
     *
     * @return \App\Enums\HealthCheckStateEnum
     */
    protected function checkDatabase($connection = null): HealthCheckStateEnum
    {
        try {
            DB::connection($connection ?: config('database.default'))->getPdo();
        } catch (Throwable $e) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) use ($e) {
                $state->description = "Could not connect to the database: `{$e->getMessage()}`";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    /**
     * @return \App\Enums\HealthCheckStateEnum
     */
    protected function checkSqlSafeUpdates(): HealthCheckStateEnum
    {
        if (config('database.default') !== 'mysql') {
            return tap(HealthCheckStateEnum::WARNING(), function (HealthCheckStateEnum $state) {
                $state->description = 'This check is only available for MySQL.';
            });
        }

        $sqlSafeUpdates = DB::select("SHOW VARIABLES LIKE 'sql_safe_updates' ")[0];
        if (! Str::of($sqlSafeUpdates->Value)->lower()->is('on')) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) {
                $state->description = '`sql_safe_updates` is disabled. Please enable it.';
            });
        }

        return HealthCheckStateEnum::OK();
    }

    /**
     * @param  array|string  $checkedSqlModes
     *
     * @return \App\Enums\HealthCheckStateEnum
     */
    protected function checkSqlMode($checkedSqlModes = 'strict_all_tables'): HealthCheckStateEnum
    {
        if (config('database.default') !== 'mysql') {
            return tap(HealthCheckStateEnum::WARNING(), function (HealthCheckStateEnum $state) {
                $state->description = 'This check is only available for MySQL.';
            });
        }

        $sqlModes = DB::select("SHOW VARIABLES LIKE 'sql_mode' ")[0];

        /* @var Collection $diffSqlModes */
        $diffSqlModes = Str::of($sqlModes->Value)
            ->lower()
            ->explode(',')
            ->pipe(function (Collection $sqlModes) use ($checkedSqlModes): Collection {
                return collect($checkedSqlModes)
                    ->transform(function (string $checkedSqlMode) {
                        return Str::of($checkedSqlMode)->lower();
                    })
                    ->diff($sqlModes);
            });
        if ($diffSqlModes->isNotEmpty()) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) use ($diffSqlModes) {
                $state->description = "`sql_mode` is not set to `{$diffSqlModes->implode('、')}`. Please set to them.";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    protected function checkTimezone(): HealthCheckStateEnum
    {
        $dbTimeZone = DB::select("SHOW VARIABLES LIKE 'time_zone' ")[0]->Value;
        Str::of($dbTimeZone)->lower()->is('system') and $dbTimeZone = DB::select("SHOW VARIABLES LIKE 'system_time_zone' ")[0]->Value;

        $dbDateTime = (new DateTime('now', new DateTimeZone($dbTimeZone)))->format('YmdH');
        $appDateTime = (new DateTime('now', new DateTimeZone($appTimezone = config('app.timezone'))))->format('YmdH');
        if ($dbDateTime !== $appDateTime) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) use ($appTimezone, $dbTimeZone) {
                $state->description = "The database timezone(`$dbTimeZone`) is not equal to app timezone(`$appTimezone`).";
            });
        }

        return HealthCheckStateEnum::OK();
    }
}
