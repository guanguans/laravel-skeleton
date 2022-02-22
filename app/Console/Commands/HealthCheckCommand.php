<?php

namespace App\Console\Commands;

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
            ->filter(function ($method) {
                return Str::startsWith($method->name, 'check');
            })
            ->reduce(function (Collection $carry, $method) {
                $state = call_user_func([$this, $method->name]);

                return $carry->add([
                    'resource' => Str::of($method->name)->replace('check', '')->__toString(),
                    'state' => $this->isHealthy($state) ? '<info>healthy</info>' : '<error>failing</error>',
                    'message' => $this->isHealthy($state) ? null : $state,
                ]);
            }, collect())
            ->pipe(function (Collection $checks) {
                $this->table(['Resource', 'State', 'Message'], $checks);
            });


        return 0;
    }

    protected function getHealthyState(): string
    {
        return 'ok';
    }

    protected function isHealthy(string $healthyState): bool
    {
        return $this->getHealthyState() === $healthyState;
    }

    protected function checkDatabase(): string
    {
        try {
            DB::connection(config('database.default'))->getPdo();

            return $this->getHealthyState();
        } catch (Throwable $e) {
            return "Could not connect to the database: `{$e->getMessage()}`";
        }
    }

    protected function checkSqlSafeUpdates(): string
    {
        $sqlSafeUpdates = DB::select("SHOW VARIABLES LIKE 'sql_safe_updates' ")[0];

        return Str::of($sqlSafeUpdates->Value)->lower()->is('on')
            ? $this->getHealthyState()
            : '`sql_safe_updates` is disabled. Please enable it.';
    }

    protected function checkStrictAllTables(): string
    {
        $sqlMode = DB::select("SHOW VARIABLES LIKE 'sql_mode' ")[0];

        return Str::of($sqlMode->Value)->lower()->contains('strict_all_tables')
            ? $this->getHealthyState()
            : '`sql_mode` is not set to strict_all_tables. Please set it.';
    }
}
