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

namespace App\Console\Commands;

use GrahamCampbell\ResultType\Error;
use GrahamCampbell\ResultType\Result;
use GrahamCampbell\ResultType\Success;
use Illuminate\Console\Command;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @see https://github.com/ans-group/laravel-health-check
 * @see https://github.com/antonioribeiro/health
 * @see https://github.com/enlightn/enlightn
 * @see https://github.com/enlightn/laravel-security-checker
 * @see https://github.com/Hi-Folks/lara-lens
 * @see https://github.com/imanghafoori1/laravel-microscope
 * @see https://github.com/Jorijn/laravel-security-checker
 * @see https://github.com/laminas/laminas-diagnostics
 * @see https://github.com/spatie/laravel-health
 */
final class HealthCheckCommand extends Command
{
    private const string RESULT_SUCCESS = '<info>ok</info>';
    private const string RESULT_ERROR = '<error>failing</error>';
    protected $signature = <<<'SIGNATURE'
        health:check
        {--only=* : Only check methods with the given name}
        {--except=* : Do not check methods with the given name}
        SIGNATURE;
    protected $description = 'Health check.';
    private array $only = [];
    private array $except = [];

    public function handle(): void
    {
        collect((new \ReflectionObject($this))->getMethods(\ReflectionMethod::IS_PRIVATE))
            ->filter(static fn (\ReflectionMethod $method) => str($method->name)->startsWith('check'))
            ->when($this->only, fn (Collection $methods) => $methods->filter(
                fn (\ReflectionMethod $method) => str($method->name)->is($this->only)
            ))
            ->when($this->except, fn (Collection $methods) => $methods->reject(
                fn (\ReflectionMethod $method) => str($method->name)->is($this->except)
            ))
            ->sortBy(static fn (\ReflectionMethod $method) => $method->name)
            ->pipe(function (Collection $methods) {
                $this
                    ->setProcessTitle('Health checking...')
                    ->withProgressBar($methods, function (\ReflectionMethod $method) use (&$checks): void {
                        $result = $this->{$method->name}();

                        \assert($result instanceof Result);

                        $checks[] = [
                            'index' => \count((array) $checks) + 1,
                            'resource' => str($method->name)->replaceFirst('check', ''),
                            'state' => $result->success()->getOrElse(self::RESULT_ERROR),
                            'message' => $result->error()
                                ->map(static fn (string $error): string => "<comment>$error</comment>")
                                ->getOrElse(self::RESULT_SUCCESS),
                        ];
                    });

                return collect($checks);
            })
            ->tap(fn (Collection $checks) => $this->newLine()->table(
                ['Index', 'Resource', 'State', 'Message'],
                $checks->all()
            ))
            ->filter(static fn (array $check): bool => self::RESULT_SUCCESS !== $check['state'])
            ->whenNotEmpty(function (): void {
                $this->components->error('Health check failed.');
            })
            ->whenEmpty(function (): void {
                $this->components->info('Health check passed.');
            });
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->only = array_merge($this->only, $this->option('only'));
        $this->except = array_merge($this->except, $this->option('except'));
    }

    /**
     * @noinspection PhpSameParameterValueInspection
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \JsonException
     */
    private function checkServiceProvider(
        array $except = [
            'intervention/image',
            'kitloong/laravel-app-logger',
            'laravel-lang/actions',
            'laravel-lang/attributes',
            'laravel-lang/config',
            'laravel-lang/http-statuses',
            'laravel-lang/lang',
            'laravel-lang/locales',
            'laravel-lang/models',
            'laravel-lang/moonshine',
            'laravel-lang/publisher',
            'laravel-lang/routes',
            'laravel/horizon',
            'laravel/scout',
            'livewire/livewire',
            'nesbot/carbon',
            'nunomaduro/termwind',
            'orchid/blade-icons',
            'spatie/eloquent-sortable',
            'spatie/laravel-http-logger',
            'spatie/laravel-signal-aware-command',
            'staudenmeir/laravel-cte',
            'watson/active',
            'whitecube/laravel-timezones',
            'wilderborn/partyline',
        ]
    ): Result {
        $this->callSilently('package:discover');

        $composer = json_decode(file_get_contents(base_path('composer.json')), true, 512, \JSON_THROW_ON_ERROR);
        $prodPackages = array_keys($composer['require'] ?? []);
        $devPackages = array_keys($composer['require-dev'] ?? []);
        $dontDiscoverPackages = $composer['extra']['laravel']['dont-discover'] ?? [];

        /** @noinspection UsingInclusionReturnValueInspection */
        $discoveredPackages = collect(require base_path('bootstrap/cache/packages.php'))->reject(
            static fn (array $map, string $package): bool => str($package)->is($except)
        );
        $shouldntDiscoverPackages = $discoveredPackages->filter(static fn (
            array $map,
            string $package
        ): bool => \in_array($package, $devPackages, true) && !\in_array($package, $dontDiscoverPackages, true));
        $indirectDiscoveredPackages = $discoveredPackages->filter(static fn (
            array $map,
            string $package
        ): bool => !\in_array($package, $prodPackages, true) && !\in_array($package, $devPackages, true));

        if ($shouldntDiscoverPackages->isNotEmpty() || $indirectDiscoveredPackages->isNotEmpty()) {
            $this->laravel->make(Dispatcher::class)->listen(
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
                                ->toJson(\JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES)
                        ),
                        $shouldntDiscoverPackages->pluck('providers')->flatten()->pipe($piper),
                        $indirectDiscoveredPackages->pipe($piper),
                        $indirectDiscoveredPackages->keys()->pipe($piper),
                        $indirectDiscoveredPackages->pluck('providers')->flatten()->pipe($piper),
                    ));
                }
            );

            return Error::create(
                $shouldntDiscoverPackages->isNotEmpty()
                    ? "The dev packages shouldn't be automatically discovered."
                    : 'The indirect discovered packages should be manually handled.'
            );
        }

        return $this->createSuccessResult();
    }

    /**
     * @noinspection PhpSameParameterValueInspection
     */
    private function checkDatabase(?string $connection = null): Result
    {
        try {
            DB::connection($connection ?: config('database.default'))->getPdo();
        } catch (\Throwable $throwable) {
            return Error::create("Could not connect to the database: `{$throwable->getMessage()}`");
        }

        return $this->createSuccessResult();
    }

    private function checkSqlSafeUpdates(): Result
    {
        if (config('database.default') !== 'mysql') {
            return Error::create('This check is only available for MySQL.');
        }

        $sqlSafeUpdates = DB::select("SHOW VARIABLES LIKE 'sql_safe_updates' ")[0];

        if (!str($sqlSafeUpdates->Value)->lower()->is('on')) {
            return Error::create('`sql_safe_updates` is disabled. Please enable it.');
        }

        return $this->createSuccessResult();
    }

    /**
     * @noinspection PhpSameParameterValueInspection
     *
     * @param list<string>|string $checkedSqlModes
     */
    private function checkSqlMode(array|string $checkedSqlModes = 'strict_all_tables'): Result
    {
        if (config('database.default') !== 'mysql') {
            return Error::create('This check is only available for MySQL.');
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
            return Error::create("`sql_mode` is not set to `{$diffSqlModes->implode('、')}`. Please set to them.");
        }

        return $this->createSuccessResult();
    }

    /**
     * @throws \Exception
     */
    private function checkTimeZone(): Result
    {
        if (config('database.default') !== 'mysql') {
            return Error::create('This check is only available for MySQL.');
        }

        $dbDateTime = DB::scalar("SELECT DATE_FORMAT(NOW(), '%Y-%m-%d %H')");
        $appDateTime = now()->format('Y-m-d H');

        if ($dbDateTime !== $appDateTime) {
            $dbTimeZone = DB::selectOne("SHOW VARIABLES LIKE 'time_zone'")->Value;

            if (str($dbTimeZone)->lower()->is('system')) {
                $dbTimeZone = DB::selectOne("SHOW VARIABLES LIKE 'system_time_zone'")->Value;
            }

            $appTimezone = now()->getTimezone()->getName();

            return Error::create("The database timezone(`$dbTimeZone`) is not equal to app timezone(`$appTimezone`).");
        }

        return $this->createSuccessResult();
    }

    /**
     * @noinspection PhpSameParameterValueInspection
     */
    private function checkPing(?string $url = null): Result
    {
        try {
            $response = Http::get($url ?: config('app.url'));

            if ($response->serverError()) {
                // return Error::create("Could not connect to the application: `{$response->body()}`");
                return Error::create("Could not connect to the application: `{$response->reason()}`");
            }
        } catch (\Throwable $throwable) {
            return Error::create("Could not connect to the application: `{$throwable->getMessage()}`");
        }

        return $this->createSuccessResult();
    }

    /**
     * @throws \JsonException
     */
    private function checkPhpExtensions(): Result
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
            static fn (Collection $missingExtensions, string $extension) => $missingExtensions->unless(
                \extension_loaded($extension),
                static fn (Collection $missingExtensions) => $missingExtensions->add($extension)
            ),
            collect()
        );

        \assert($missingExtensions instanceof Collection);

        if ($missingExtensions->isNotEmpty()) {
            return Error::create("The following PHP extensions are missing: `{$missingExtensions->implode('、')}`.");
        }

        $processResult = Process::run([
            ...app(Composer::class)->findComposer(), 'check-platform-reqs', '--format', 'json', '--ansi', '-v',
        ])->throw();
        $errorExtensions = collect(json_decode($processResult->output(), true, 512, \JSON_THROW_ON_ERROR))->filter(
            static fn (array $item): bool => 'success' !== $item['status']
        );

        if ($errorExtensions->isNotEmpty()) {
            return Error::create("The following PHP extensions have errors: `{$errorExtensions->pluck('name')->implode('、')}`.");
        }

        return $this->createSuccessResult();
    }

    private function checkDiskSpace(int $limit = 100): Result
    {
        $freeSpace = disk_free_space(base_path());
        $diskSpace = \sprintf('%.1f', $freeSpace / (1024 * 1024));

        if ($limit > $diskSpace) {
            return Error::create("The disk space is less than 100MB: `$diskSpace`.");
        }

        $diskSpace = \sprintf('%.1f', $freeSpace / (1024 * 1024 * 1024));

        if (1 > $diskSpace) {
            return Error::create("The disk space is less than 1GB: `$diskSpace`.");
        }

        return $this->createSuccessResult();
    }

    /**
     * @noinspection PhpSameParameterValueInspection
     */
    private function checkMemoryLimit(int $limit = 256): Result
    {
        $inis = collect(ini_get_all())->filter(static fn (array $value, string $key): bool => str_contains($key, 'memory_limit'));

        if ($inis->isEmpty()) {
            return Error::create('The memory limit is not set.');
        }

        $localValue = $inis->first()['local_value'];

        if (0 < $localValue && $localValue < $limit) {
            return Error::create("The memory limit is less than {$limit}M: `$localValue`.");
        }

        return $this->createSuccessResult();
    }

    private function createSuccessResult(): Result
    {
        return Success::create(self::RESULT_SUCCESS);
    }
}
