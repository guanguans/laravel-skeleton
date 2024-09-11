<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * @see https://github.com/vitodeploy/vito/blob/1.x/app/Console/Commands/MigrateFromMysqlToSqlite.php
 */
class MigrateFromMysqlToSqlite extends Command
{
    protected $signature = 'migrate-from-mysql-to-sqlite';

    protected $description = 'Migrate from Mysql to SQLite';

    public function handle(): void
    {
        $this->info('Migrating from Mysql to SQLite...');

        File::exists(storage_path('database.sqlite'))
            ? File::delete(storage_path('database.sqlite'))
            : null;

        File::put(storage_path('database.sqlite'), '');

        config(['database.default' => 'sqlite']);

        $this->call('migrate', ['--force' => true]);

        // $this->migrateModel(\App\Models\Server::class);
        // $this->migrateModel(\App\Models\ServerLog::class);
        $this->migrateModel(\App\Models\User::class);

        $env = File::get(base_path('.env'));
        $env = str_replace(['DB_CONNECTION=mysql', 'DB_DATABASE=vito'], ['DB_CONNECTION=sqlite', ''], $env);
        File::put(base_path('.env'), $env);

        $this->info('Migrated from Mysql to SQLite');
    }

    /**
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     */
    private function migrateModel(string $model): void
    {
        $this->info("Migrating model: {$model}");

        config(['database.default' => 'mysql']);

        $rows = $model::where('id', '>', 0)->get();

        foreach ($rows as $row) {
            DB::connection('sqlite')->table($row->getTable())->insert($row->getAttributes());
        }

        $this->info("Migrated model: {$model}");
    }
}
