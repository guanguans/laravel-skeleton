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

namespace App\Console\Commands\Concerns;

use Symfony\Component\Console\Command\Command;

/**
 * @see \Illuminate\Database\Console\Migrations\MigrateCommand
 *
 * @mixin \Illuminate\Console\Command
 */
trait Graceful
{
    /**
     * @throws \Throwable
     */
    public function graceful(callable $callback): int
    {
        try {
            $callback();
        } catch (\Throwable $throwable) {
            if ($this->hasOption('graceful') && $this->option('graceful')) {
                $this->components->warn($throwable->getMessage());

                return Command::SUCCESS;
            }

            throw $throwable;
        }

        return Command::SUCCESS;
    }
}
