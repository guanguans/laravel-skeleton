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

namespace Database\Seeders\Concerns;

use Illuminate\Console\View\Components\TwoColumnDetail;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

/**
 * @see https://glimpse.sh/blog/idempotent-database-seeding-laravel-preview-environments
 */
trait CanSeedOncePerDatabase
{
    protected string $seedersTable = 'seeders';
    protected bool $seedersTableExists = false;

    public function callOncePerDatabase(string $class, bool $silent = false, array $parameters = []): void
    {
        if ($this->seederHasAlreadyBeenCalled($class)) {
            if (false === $silent && isset($this->command)) {
                (new TwoColumnDetail($this->command->getOutput()))->render(
                    $class,
                    '<fg=gray>Seeder had already run on this database</> <fg=yellow;options=bold>SKIPPING</>'
                );

                $this->command->getOutput()->writeln('');
            }

            return;
        }

        $this->call($class, $silent, $parameters);

        $this->markSeederAsCalled($class);
    }

    protected function seederHasAlreadyBeenCalled(string $class): bool
    {
        $this->createSeedersTableIfNotExists();

        return DB::table($this->seedersTable)
            ->where('seeder', $class)
            ->exists();
    }

    protected function markSeederAsCalled(string $class): void
    {
        $this->createSeedersTableIfNotExists();

        DB::table($this->seedersTable)
            ->insert(['seeder' => $class]);
    }

    protected function createSeedersTableIfNotExists(): void
    {
        if ($this->seedersTableExists) {
            return;
        }

        $schema = DB::connection()->getSchemaBuilder();

        if (!$schema->hasTable($this->seedersTable)) {
            $schema->create($this->seedersTable, static function (Blueprint $table): void {
                $table->string('seeder')->unique();
            });
        }

        $this->seedersTableExists = true;
    }
}
