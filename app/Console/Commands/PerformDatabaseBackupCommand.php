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

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * @see https://github.com/pinkary-project/pinkary.com
 */
final class PerformDatabaseBackupCommand extends Command
{
    protected $signature = 'perform:database-backup';
    protected $description = 'Perform a database backup.';

    public function handle(): void
    {
        $filename = 'backup-'.now()->timestamp.'.sql';

        File::copy(database_path('database.sqlite'), database_path('backups/'.$filename));

        $glob = File::glob(database_path('backups/*.sql'));

        collect($glob)->sort()->reverse()->slice(4)->filter(
            static fn (mixed $backup): bool => \is_string($backup),
        )->each(static fn (string $backup): bool => File::delete($backup));
    }
}
