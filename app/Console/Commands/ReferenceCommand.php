<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('reference')]
final class ReferenceCommand extends Command
{
    /**
     * @see https://github.com/EncoreDigitalGroup/laravel-cache-prune/blob/main/src/Console/Commands/CachePruneCommand.php
     * @see https://github.com/jhdxr/laravel-prune-db-cache/blob/master/src/Command/PruneDbCache.php
     * @see https://github.com/pinkary-project/pinkary.com/blob/main/app/Console/Commands/PerformDatabaseBackupCommand.php
     * @see https://github.com/RonasIT/laravel-project-initializator/blob/main/src/Commands/InitCommand.php
     * @see https://github.com/vitodeploy/vito/blob/3.x/app/Console/Commands/MigrateFromMysqlToSqlite.php
     */
    public function handle(): void {}
}
