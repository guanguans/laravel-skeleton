<?php

/** @noinspection EmptyClassInspection */

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
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * @see https://github.com/vitodeploy/vito/blob/2.x/app/Console/Commands/MigrateFromMysqlToSqlite.php
 */
#[AsCommand('migrate-from-mysql-to-sqlite')]
final class MigrateFromMysqlToSqlite extends Command {}
