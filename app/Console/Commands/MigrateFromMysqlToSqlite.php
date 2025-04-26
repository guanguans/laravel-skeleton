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

use App\Models\HttpLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * @see https://github.com/vitodeploy/vito/blob/2.x/app/Console/Commands/MigrateFromMysqlToSqlite.php
 */
final class MigrateFromMysqlToSqlite extends Command
{
    protected $signature = 'migrate-from-mysql-to-sqlite';
    protected $description = 'Migrate from Mysql to SQLite';

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): void
    {
        $this->components->info('Migrating from Mysql to SQLite...');

        if (File::exists(storage_path('database.sqlite'))) {
            File::delete(storage_path('database.sqlite'));
        }

        File::put(storage_path('database.sqlite'), '');

        config(['database.default' => 'sqlite']);

        $this->call('migrate', ['--force' => true]);

        $this->migrateModel(HttpLog::class);
        $this->migrateModel(User::class);

        $env = File::get(base_path('.env'));
        $env = str_replace(['DB_CONNECTION=mysql', 'DB_DATABASE=vito'], ['DB_CONNECTION=sqlite', ''], $env);
        File::put(base_path('.env'), $env);

        $this->components->info('Migrated from Mysql to SQLite');
    }

    /**
     * @param class-string<\Eloquence\Database\Model> $model
     */
    private function migrateModel(string $model): void
    {
        $this->components->info("Migrating model: $model");

        config(['database.default' => 'mysql']);

        $rows = $model::query()->where('id', '>', 0)->get();

        foreach ($rows as $row) {
            DB::connection('sqlite')->table($row->getTable())->insert($row->getAttributes());
        }

        $this->components->info("Migrated model: $model");
    }
}
