<?php

/** @noinspection MethodVisibilityInspection */

namespace Database\Seeders\Concerns;

use Illuminate\Console\View\Components\TwoColumnDetail;
use Illuminate\Support\Facades\DB;

/**
 * @see https://glimpse.sh/blog/idempotent-database-seeding-laravel-preview-environments
 */
trait CanSeedOncePerDatabase
{
    protected string $seedersTable = 'seeders';

    protected bool $seedersTableExists = false;

    public function callOncePerDatabase($class, $silent = false, array $parameters = []): void
    {
        if ($this->seederHasAlreadyBeenCalled($class)) {
            if ($silent === false && isset($this->command)) {
                with(new TwoColumnDetail($this->command->getOutput()))->render(
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

    protected function seederHasAlreadyBeenCalled($class): bool
    {
        $this->createSeedersTableIfNotExists();

        return DB::table($this->seedersTable)
            ->where('seeder', $class)
            ->exists();
    }

    protected function markSeederAsCalled($class): void
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

        if (! $schema->hasTable($this->seedersTable)) {
            $schema->create($this->seedersTable, static function ($table) {
                $table->string('seeder')->unique();
            });
        }

        $this->seedersTableExists = true;
    }
}
