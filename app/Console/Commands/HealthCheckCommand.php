<?php

namespace App\Console\Commands;

use App\Enums\HealthCheckStateEnum;
use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
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
     * @var array
     */
    protected $except = [
        '*Queue'
    ];

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
                return (bool)(string)Str::of($method->name)->pipe(function (Stringable $name) {
                    return $name->startsWith('check') && ! $name->is($this->except);
                });
            })
            ->sortBy(function (ReflectionMethod $method) {
                return $method->name;
            })
            ->pipe(function (Collection $methods) {
                $this->withProgressBar($methods, function ($method) use (&$checks) {
                    /* @var HealthCheckStateEnum $state */
                    $state = call_user_func([$this, $method->name]);

                    $checks[] = [
                        'index' => count((array)$checks) + 1,
                        'resource' => Str::of($method->name)->replaceFirst('check', ''),
                        'state' => $state,
                        'message' => $state->description,
                    ];
                });

                $this->newLine();
                $this->table(['Index', 'Resource', 'State', 'Message'], $checks);

                return collect($checks);
            })
            ->tap(function (Collection $checks) {
                $checks
                    ->filter(function ($check) {
                        return $check['state']->isNot(HealthCheckStateEnum::OK);
                    })
                    ->whenNotEmpty(function (Collection $notOkChecks) {
                        // event(new HealthCheckFailedEvent($notOkChecks));
                        $this->error('Health check failed.');

                        return $notOkChecks;
                    })
                    ->whenEmpty(function (Collection $notOkChecks) {
                        // event(new HealthCheckPassedEvent());
                        $this->info('Health check passed.');

                        return $notOkChecks;
                    });
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

    /**
     * @return \App\Enums\HealthCheckStateEnum
     * @throws \Exception
     */
    protected function checkTimeZone(): HealthCheckStateEnum
    {
        if (config('database.default') !== 'mysql') {
            return tap(HealthCheckStateEnum::WARNING(), function (HealthCheckStateEnum $state) {
                $state->description = 'This check is only available for MySQL.';
            });
        }

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

    /**
     * @param  null|string  $url
     *
     * @return \App\Enums\HealthCheckStateEnum
     */
    protected function checkPing(?string $url = null): HealthCheckStateEnum
    {
        $url = $url ?: config('app.url');

        $response = Http::get($url);
        if ($response->failed()) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) use ($response) {
                $state->description = "Could not connect to the application: `{$response->body()}`";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    /**
     * @return \App\Enums\HealthCheckStateEnum
     */
    protected function checkPhpVersion(): HealthCheckStateEnum
    {
        if (version_compare(PHP_VERSION, '7.3.0', '<')) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) {
                $state->description = 'PHP version is less than 7.3.0.';
            });
        }

        return HealthCheckStateEnum::OK();
    }

    protected function checkPhpExtensions(): HealthCheckStateEnum
    {
        $extensions = [
            'curl',
            'gd',
            'mbstring',
            'openssl',
            'pdo',
            'pdo_mysql',
            'xml',
            'zip',
            'swoole',
        ];

        /* @var Collection $missingExtensions */
        $missingExtensions = collect($extensions)
            ->reduce(function (Collection $missingExtensions, $extension) {
                return $missingExtensions->when(! extension_loaded($extension), function (Collection $missingExtensions) use ($extension) {
                    return $missingExtensions->add($extension);
                });
            }, collect());

        if ($missingExtensions->isNotEmpty()) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) use ($missingExtensions) {
                $state->description = "The following PHP extensions are missing: `{$missingExtensions->implode('、')}`.";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    /**
     * @return \App\Enums\HealthCheckStateEnum
     */
    protected function checkDiskSpace(): HealthCheckStateEnum
    {
        $freeSpace = disk_free_space(base_path());
        $diskSpace = sprintf('%.1f', $freeSpace / (1024 * 1024));
        if ($diskSpace < 100) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) use ($diskSpace) {
                $state->description = "The disk space is less than 100MB: `$diskSpace`.";
            });
        }

        $diskSpace = sprintf('%.1f', $freeSpace / (1024 * 1024 * 1024));
        if ($diskSpace < 1) {
            return tap(HealthCheckStateEnum::WARNING(), function (HealthCheckStateEnum $state) use ($diskSpace) {
                $state->description = "The disk space is less than 1GB: `$diskSpace`.";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    protected function checkMemoryLimit(int $limit = 128): HealthCheckStateEnum
    {
        $inis = collect(ini_get_all())->filter(function ($value, $key) {
            return str_contains($key, 'memory_limit');
        });

        if ($inis->isEmpty()) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) {
                $state->description = "The memory limit is not set.";
            });
        }

        $localValue = $inis->first()['local_value'];
        if ($localValue < $limit) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) use ($limit, $localValue) {
                $state->description = "The memory limit is less than {$limit}M: `$localValue`.";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    protected function checkQueue(): HealthCheckStateEnum
    {
        if (! Queue::connected()) {
            return tap(HealthCheckStateEnum::FAILING(), function (HealthCheckStateEnum $state) {
                $state->description = "The queue is not connected.";
            });
        }

        return HealthCheckStateEnum::OK();
    }
}
