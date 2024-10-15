<?php

/** @noinspection PhpUnusedPrivateMethodInspection */

namespace App\Console\Commands;

use App\Enums\HealthCheckStateEnum;
use Illuminate\Console\Command;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use ReflectionMethod;
use ReflectionObject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class HealthCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health:check
        {--only=* : Only check methods with the given name}
        {--except=* : Do not check methods with the given name}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Health check.';

    private array $only = [];

    private array $except = [];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        collect((new ReflectionObject($this))->getMethods(ReflectionMethod::IS_PRIVATE))
            ->filter(static fn (ReflectionMethod $method) => str($method->name)->startsWith('check'))
            ->when($this->only, fn (Collection $methods) => $methods->filter(
                fn (ReflectionMethod $method) => str($method->name)->is($this->only)
            ))
            ->when($this->except, fn (Collection $methods) => $methods->reject(
                fn (ReflectionMethod $method) => str($method->name)->is($this->except)
            ))
            ->sortBy(static fn (ReflectionMethod $method) => $method->name)
            ->pipe(function (Collection $methods) {
                $this
                    ->setProcessTitle('Health checking...')
                    ->withProgressBar($methods, function ($method) use (&$checks): void {
                        $state = $this->{$method->name}();

                        \assert($state instanceof HealthCheckStateEnum);

                        $checks[] = [
                            'index' => \count((array) $checks) + 1,
                            'resource' => str($method->name)->replaceFirst('check', ''),
                            'state' => $state,
                            'message' => $state->description,
                        ];
                    });

                return collect($checks);
            })
            ->tap(fn (Collection $checks) => $this->newLine()->table(
                ['Index', 'Resource', 'State', 'Message'],
                $checks->all()
            ))
            ->filter(static fn ($check): bool => $check['state']->isNot(HealthCheckStateEnum::OK))
            ->whenNotEmpty(function (): void {
                $this->components->error('Health check failed.');
            })
            ->whenEmpty(function (): void {
                $this->components->info('Health check passed.');
            });
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->only = array_merge($this->only, $this->option('only'));
        $this->except = array_merge($this->except, $this->option('except'));
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \JsonException
     *
     * @noinspection PhpSameParameterValueInspection
     */
    private function checkServiceProvider(
        array $except = [
            'intervention/image',
            'nesbot/carbon',
            'nunomaduro/termwind',
            'spatie/eloquent-sortable',
            'spatie/laravel-signal-aware-command',
            'kitloong/laravel-app-logger',
            'laravel-lang/actions',
            'laravel-lang/attributes',
            'laravel-lang/config',
            'laravel-lang/http-statuses',
            'laravel-lang/lang',
            'laravel-lang/locales',
            'laravel-lang/models',
            'laravel-lang/publisher',
            'laravel-lang/routes',
            'laravel/scout',
            'livewire/livewire',
            'orchid/blade-icons',
            'spatie/laravel-http-logger',
            'staudenmeir/laravel-cte',
            'watson/active',
            'wilderborn/partyline',
        ]
    ): HealthCheckStateEnum {
        $this->callSilently('package:discover');

        $composer = json_decode(file_get_contents(base_path('composer.json')), true, 512, JSON_THROW_ON_ERROR);
        $prodPackages = array_keys($composer['require'] ?? []);
        $devPackages = array_keys($composer['require-dev'] ?? []);
        $dontDiscoverPackages = $composer['extra']['laravel']['dont-discover'] ?? [];
        $discoveredPackages = collect(require base_path('bootstrap/cache/packages.php'))->reject(
            static fn (array $map, string $package): bool => str($package)->is($except)
        );
        $shouldntDiscoverPackages = $discoveredPackages->filter(static fn (
            array $map,
            string $package
        ): bool => \in_array($package, $devPackages, true) && ! \in_array($package, $dontDiscoverPackages, true));
        $indirectDiscoveredPackages = $discoveredPackages->filter(static fn (
            array $map,
            string $package
        ): bool => ! \in_array($package, $prodPackages, true) && ! \in_array($package, $devPackages, true));

        if ($shouldntDiscoverPackages->isNotEmpty() || $indirectDiscoveredPackages->isNotEmpty()) {
            return tap(
                HealthCheckStateEnum::FAILING(),
                function (HealthCheckStateEnum $state) use ($shouldntDiscoverPackages, $indirectDiscoveredPackages): void {
                    $state->description = $shouldntDiscoverPackages->isNotEmpty()
                        ? "The dev packages shouldn't be automatically discovered."
                        : 'The indirect discovered packages should be manually handled.';

                    $this->laravel->make('events')->listen(
                        CommandFinished::class,
                        function () use ($shouldntDiscoverPackages, $indirectDiscoveredPackages): void {
                            $this->warn(\sprintf(
                                <<<'WARN'
                                    The dev packages should be added to `extra.laravel.dont-discover` in `composer.json`:
                                    %s

                                    The dev service providers should be manually registered to dev environment:
                                    %s

                                    The indirect discovered packages should be manually handled:
                                    %s
                                    %s
                                    %s
                                    WARN,
                                $shouldntDiscoverPackages->keys()->pipe(
                                    $piper = static fn (Collection $collection) => $collection
                                        ->sort()
                                        ->values()
                                        ->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                                ),
                                $shouldntDiscoverPackages->pluck('providers')->flatten()->pipe($piper),
                                $indirectDiscoveredPackages->pipe($piper),
                                $indirectDiscoveredPackages->keys()->pipe($piper),
                                $indirectDiscoveredPackages->pluck('providers')->flatten()->pipe($piper),
                            ));
                        }
                    );
                }
            );
        }

        return HealthCheckStateEnum::OK();
    }

    private function checkDatabase(?string $connection = null): HealthCheckStateEnum
    {
        try {
            DB::connection($connection ?: config('database.default'))->getPdo();
        } catch (Throwable $throwable) {
            return tap(
                HealthCheckStateEnum::FAILING(),
                static function (HealthCheckStateEnum $state) use ($throwable): void {
                    $state->description = "Could not connect to the database: `{$throwable->getMessage()}`";
                }
            );
        }

        return HealthCheckStateEnum::OK();
    }

    private function checkSqlSafeUpdates(): HealthCheckStateEnum
    {
        if (config('database.default') !== 'mysql') {
            return tap(HealthCheckStateEnum::WARNING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'This check is only available for MySQL.';
            });
        }

        $sqlSafeUpdates = DB::select("SHOW VARIABLES LIKE 'sql_safe_updates' ")[0];
        if (! str($sqlSafeUpdates->Value)->lower()->is('on')) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state): void {
                $state->description = '`sql_safe_updates` is disabled. Please enable it.';
            });
        }

        return HealthCheckStateEnum::OK();
    }

    /**
     * @param  array<string>|string  $checkedSqlModes
     */
    private function checkSqlMode(array|string $checkedSqlModes = 'strict_all_tables'): HealthCheckStateEnum
    {
        if (config('database.default') !== 'mysql') {
            return tap(HealthCheckStateEnum::WARNING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'This check is only available for MySQL.';
            });
        }

        $sqlModes = DB::select("SHOW VARIABLES LIKE 'sql_mode' ")[0];

        $diffSqlModes = str($sqlModes->Value)
            ->lower()
            ->explode(',')
            ->pipe(
                static fn (Collection $sqlModes): Collection => collect($checkedSqlModes)
                    ->transform(static fn (string $checkedSqlMode) => str($checkedSqlMode)->lower())
                    ->diff($sqlModes)
            );

        \assert($diffSqlModes instanceof Collection);

        if ($diffSqlModes->isNotEmpty()) {
            return tap(
                HealthCheckStateEnum::FAILING(),
                static function (HealthCheckStateEnum $state) use ($diffSqlModes): void {
                    $state->description = "`sql_mode` is not set to `{$diffSqlModes->implode('、')}`. Please set to them.";
                }
            );
        }

        return HealthCheckStateEnum::OK();
    }

    /**
     * @throws \Exception
     */
    private function checkTimeZone(): HealthCheckStateEnum
    {
        if (config('database.default') !== 'mysql') {
            return tap(HealthCheckStateEnum::WARNING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'This check is only available for MySQL.';
            });
        }

        $dbDateTime = DB::scalar("SELECT DATE_FORMAT(NOW(), '%Y-%m-%d %H')");
        $appDateTime = now()->format('Y-m-d H');
        if ($dbDateTime !== $appDateTime) {
            return tap(
                HealthCheckStateEnum::FAILING(),
                static function (HealthCheckStateEnum $state): void {
                    $dbTimeZone = DB::selectOne("SHOW VARIABLES LIKE 'time_zone'")->Value;
                    if (str($dbTimeZone)->lower()->is('system')) {
                        $dbTimeZone = DB::selectOne("SHOW VARIABLES LIKE 'system_time_zone'")->Value;
                    }

                    $appTimezone = now()->getTimezone()->getName();

                    $state->description = "The database timezone(`$dbTimeZone`) is not equal to app timezone(`$appTimezone`).";
                }
            );
        }

        return HealthCheckStateEnum::OK();
    }

    private function checkPing(?string $url = null): HealthCheckStateEnum
    {
        try {
            $response = Http::get($url ?: config('app.url'));
            if ($response->serverError()) {
                return tap(
                    HealthCheckStateEnum::FAILING(),
                    static function (HealthCheckStateEnum $state) use ($response): void {
                        // $state->description = "Could not connect to the application: `{$response->body()}`";
                        $state->description = "Could not connect to the application: `{$response->reason()}`";
                    }
                );
            }
        } catch (\Throwable $throwable) {
            return tap(
                HealthCheckStateEnum::FAILING(),
                static function (HealthCheckStateEnum $state) use ($throwable): void {
                    $state->description = "Could not connect to the application: `{$throwable->getMessage()}`";
                }
            );
        }

        return HealthCheckStateEnum::OK();
    }

    private function checkPhpVersion(): HealthCheckStateEnum
    {
        if (PHP_VERSION_ID < 80100) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'PHP version is less than 8.1.0.';
            });
        }

        return HealthCheckStateEnum::OK();
    }

    private function checkPhpExtensions(): HealthCheckStateEnum
    {
        $missingExtensions = collect([
            'curl',
            'gd',
            'mbstring',
            'openssl',
            'pdo',
            'pdo_mysql',
            'xml',
            'zip',
        ])->reduce(
            static fn (Collection $missingExtensions, $extension) => $missingExtensions->unless(
                \extension_loaded($extension),
                static fn (Collection $missingExtensions) => $missingExtensions->add($extension)
            ),
            collect()
        );

        \assert($missingExtensions instanceof Collection);

        if ($missingExtensions->isNotEmpty()) {
            return tap(
                HealthCheckStateEnum::FAILING(),
                static function (HealthCheckStateEnum $state) use ($missingExtensions): void {
                    $state->description = "The following PHP extensions are missing: `{$missingExtensions->implode('、')}`.";
                }
            );
        }

        return HealthCheckStateEnum::OK();
    }

    private function checkDiskSpace(): HealthCheckStateEnum
    {
        $freeSpace = disk_free_space(base_path());
        $diskSpace = \sprintf('%.1f', $freeSpace / (1024 * 1024));
        if ($diskSpace < 100) {
            return tap(
                HealthCheckStateEnum::FAILING(),
                static function (HealthCheckStateEnum $state) use ($diskSpace): void {
                    $state->description = "The disk space is less than 100MB: `$diskSpace`.";
                }
            );
        }

        $diskSpace = \sprintf('%.1f', $freeSpace / (1024 * 1024 * 1024));
        if ($diskSpace < 1) {
            return tap(
                HealthCheckStateEnum::WARNING(),
                static function (HealthCheckStateEnum $state) use ($diskSpace): void {
                    $state->description = "The disk space is less than 1GB: `$diskSpace`.";
                }
            );
        }

        return HealthCheckStateEnum::OK();
    }

    private function checkMemoryLimit(int $limit = 256): HealthCheckStateEnum
    {
        $inis = collect(ini_get_all())->filter(static fn ($value, $key): bool => str_contains($key, 'memory_limit'));
        if ($inis->isEmpty()) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'The memory limit is not set.';
            });
        }

        $localValue = $inis->first()['local_value'];
        if ($localValue > 0 && $localValue < $limit) {
            return tap(
                HealthCheckStateEnum::FAILING(),
                static function (HealthCheckStateEnum $state) use ($limit, $localValue): void {
                    $state->description = "The memory limit is less than {$limit}M: `$localValue`.";
                }
            );
        }

        return HealthCheckStateEnum::OK();
    }

    private function checkQueue(): HealthCheckStateEnum
    {
        if (! Queue::connected()) {
            return tap(HealthCheckStateEnum::FAILING(), static function (HealthCheckStateEnum $state): void {
                $state->description = 'The queue is not connected.';
            });
        }

        return HealthCheckStateEnum::OK();
    }
}
