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
        '*Queue',
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
     */
    public function handle(): int
    {
        collect((new ReflectionObject($this))->getMethods(ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE))
            ->filter(static fn (ReflectionMethod $method) => Str::of($method->name)->startsWith('check'))
            ->reject(fn (ReflectionMethod $method) => Str::of($method->name)->is($this->except))
            ->sortBy(static fn (ReflectionMethod $method) => $method->name)
            ->pipe(function (Collection $methods) {
                $this->withProgressBar($methods, function ($method) use (&$checks): void {
                    /** @var HealthCheckStateEnum $state */
                    $state = \call_user_func([$this, $method->name]);

                    $checks[] = [
                        'index' => \count((array) $checks) + 1,
                        'resource' => Str::of($method->name)->replaceFirst('check', ''),
                        'state' => $state,
                        'message' => $state->description,
                    ];
                });

                $this->newLine();
                $this->table(['Index', 'Resource', 'State', 'Message'], $checks);

                return collect($checks);
            })
            ->filter(static fn ($check): bool => $check['state']->isNot(HealthCheckStateEnum::OK))
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

        return self::SUCCESS;
    }

    protected function checkDatabase($connection = null): HealthCheckStateEnum
    {
        try {
            DB::connection($connection ?: config('database.default'))->getPdo();
        } catch (Throwable $throwable) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state) use ($throwable): void {
                $state->description = "Could not connect to the database: `{$throwable->getMessage()}`";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    protected function checkSqlSafeUpdates(): HealthCheckStateEnum
    {
        if (config('database.default') !== 'mysql') {
            return tap(HealthCheckStateEnum::WARNING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'This check is only available for MySQL.';
            });
        }

        $sqlSafeUpdates = DB::select("SHOW VARIABLES LIKE 'sql_safe_updates' ")[0];
        if (! Str::of($sqlSafeUpdates->Value)->lower()->is('on')) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state): void {
                $state->description = '`sql_safe_updates` is disabled. Please enable it.';
            });
        }

        return HealthCheckStateEnum::OK();
    }

    /**
     * @param  array|string  $checkedSqlModes
     */
    protected function checkSqlMode($checkedSqlModes = 'strict_all_tables'): HealthCheckStateEnum
    {
        if (config('database.default') !== 'mysql') {
            return tap(HealthCheckStateEnum::WARNING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'This check is only available for MySQL.';
            });
        }

        $sqlModes = DB::select("SHOW VARIABLES LIKE 'sql_mode' ")[0];

        /** @var Collection $diffSqlModes */
        $diffSqlModes = Str::of($sqlModes->Value)
            ->lower()
            ->explode(',')
            ->pipe(static fn (Collection $sqlModes): Collection => collect($checkedSqlModes)
                ->transform(static fn (string $checkedSqlMode) => Str::of($checkedSqlMode)->lower())
                ->diff($sqlModes));
        if ($diffSqlModes->isNotEmpty()) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state) use ($diffSqlModes): void {
                $state->description = "`sql_mode` is not set to `{$diffSqlModes->implode('、')}`. Please set to them.";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    /**
     * @throws \Exception
     */
    protected function checkTimeZone(): HealthCheckStateEnum
    {
        if (config('database.default') !== 'mysql') {
            return tap(HealthCheckStateEnum::WARNING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'This check is only available for MySQL.';
            });
        }

        $dbTimeZone = DB::select("SHOW VARIABLES LIKE 'time_zone' ")[0]->Value;
        Str::of($dbTimeZone)->lower()->is('system') and $dbTimeZone = DB::select("SHOW VARIABLES LIKE 'system_time_zone' ")[0]->Value;

        $dbDateTime = (new DateTime('now', new DateTimeZone($dbTimeZone)))->format('YmdH');
        $appDateTime = (new DateTime('now', new DateTimeZone($appTimezone = config('app.timezone'))))->format('YmdH');
        if ($dbDateTime !== $appDateTime) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state) use ($appTimezone, $dbTimeZone): void {
                $state->description = "The database timezone(`$dbTimeZone`) is not equal to app timezone(`$appTimezone`).";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    protected function checkPing(?string $url = null): HealthCheckStateEnum
    {
        $url = $url ?: config('app.url');

        $response = Http::get($url);
        if ($response->failed()) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state) use ($response): void {
                $state->description = "Could not connect to the application: `{$response->body()}`";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    protected function checkPhpVersion(): HealthCheckStateEnum
    {
        if (PHP_VERSION_ID < 80100) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'PHP version is less than 8.1.0.';
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

        /** @var Collection $missingExtensions */
        $missingExtensions = collect($extensions)
            ->reduce(static fn (Collection $missingExtensions, $extension) => $missingExtensions->when(! \extension_loaded($extension), static fn (Collection $missingExtensions) => $missingExtensions->add($extension)), collect());

        if ($missingExtensions->isNotEmpty()) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state) use ($missingExtensions): void {
                $state->description = "The following PHP extensions are missing: `{$missingExtensions->implode('、')}`.";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    protected function checkDiskSpace(): HealthCheckStateEnum
    {
        $freeSpace = disk_free_space(base_path());
        $diskSpace = sprintf('%.1f', $freeSpace / (1024 * 1024));
        if ($diskSpace < 100) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state) use ($diskSpace): void {
                $state->description = "The disk space is less than 100MB: `$diskSpace`.";
            });
        }

        $diskSpace = sprintf('%.1f', $freeSpace / (1024 * 1024 * 1024));
        if ($diskSpace < 1) {
            return tap(HealthCheckStateEnum::WARNING(), static function (HealthCheckStateEnum $state) use ($diskSpace): void {
                $state->description = "The disk space is less than 1GB: `$diskSpace`.";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    protected function checkMemoryLimit(int $limit = 128): HealthCheckStateEnum
    {
        $inis = collect(ini_get_all())->filter(static fn ($value, $key): bool => str_contains($key, 'memory_limit'));

        if ($inis->isEmpty()) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'The memory limit is not set.';
            });
        }

        $localValue = $inis->first()['local_value'];
        if ($localValue < $limit) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state) use ($limit, $localValue): void {
                $state->description = "The memory limit is less than {$limit}M: `$localValue`.";
            });
        }

        return HealthCheckStateEnum::OK();
    }

    protected function checkQueue(): HealthCheckStateEnum
    {
        if (! Queue::connected()) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'The queue is not connected.';
            });
        }

        return HealthCheckStateEnum::OK();
    }
}
