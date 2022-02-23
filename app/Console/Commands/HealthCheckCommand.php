<?php

namespace App\Console\Commands;

use Exception;
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
                return Str::startsWith($method->name, 'check');
            })
            ->reduce(function (Collection $carry, ReflectionMethod $method) {
                try {
                    $result = call_user_func([$this, $method->name]);
                } catch (Throwable $e) {
                    $result = $e->getMessage();
                }

                return $carry->add([
                    'resource' => Str::of($method->name)->replaceFirst('check', '')->__toString(),
                    'state' => $this->isHealthy($result) ? '<info>healthy</info>' : '<error>failing</error>',
                    'message' => $this->isHealthy($result) ? null : $result,
                ]);
            }, collect())
            ->pipe(function (Collection $checks) {
                $this->table(['Resource', 'State', 'Message'], $checks);
            });


        return 0;
    }

    protected function isHealthy($healthyState): bool
    {
        return true === $healthyState;
    }

    protected function checkDatabase(): bool
    {
        try {
            DB::connection(config('database.default'))->getPdo();
        } catch (Throwable $e) {
            throw new Exception("Could not connect to the database: `{$e->getMessage()}`");
        }

        return true;
    }

    protected function checkSqlSafeUpdates(): bool
    {
        $sqlSafeUpdates = DB::select("SHOW VARIABLES LIKE 'sql_safe_updates' ")[0];
        if (! Str::of($sqlSafeUpdates->Value)->lower()->is('on')) {
            throw new Exception('`sql_safe_updates` is disabled. Please enable it.');
        }

        return true;
    }

    protected function checkStrictAllTables(): bool
    {
        $sqlMode = DB::select("SHOW VARIABLES LIKE 'sql_mode' ")[0];
        if (! Str::of($sqlMode->Value)->lower()->contains('strict_all_tables')) {
            throw new Exception('`sql_mode` is not set to strict_all_tables. Please set it.');
        }

        return true;
    }
}
