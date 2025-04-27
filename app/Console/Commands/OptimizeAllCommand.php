<?php

/** @noinspection PhpUnusedAliasInspection */

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

use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ExecutableFinder;

final class OptimizeAllCommand extends Command
{
    protected $signature = 'optimize:all {--f|force : Force optimize.}';
    protected $description = 'Optimize all.';

    /**
     * @noinspection PhpUndefinedMethodInspection
     */
    public function handle(): void
    {
        if (!$this->option('force') && $this->getLaravel()->isProduction()) {
            $this->components->warn('Please use --force option in production.');

            return;
        }

        $this->components->info('Optimizing all...');

        foreach (
            [
                'config:cache',
                'event:cache',
                'route:cache',
                'view:cache',
            ] as $command
        ) {
            try {
                $this->components->task($command, fn () => $this->call($command, ['--ansi' => true, '-v' => true]));
            } catch (\Throwable $throwable) {
                // $this->consoleLogger()->error($throwable->getMessage());
                $this->components->error($throwable->getMessage());
            }
        }

        try {
            $command = \sprintf(
                '%s %s dump-autoload --no-interaction --optimize --ansi -v',
                (new ExecutableFinder)->find('php8.1') ?: Application::phpBinary(),
                (new ExecutableFinder)->find('composer2')
                    ?: (new ExecutableFinder)->find('composer')
                    ?: implode(' ', resolve(Composer::class)->findComposer()),
            );

            $this->components->task($command, fn () => $this->processHelperMustRun(
                cmd: $command,
                // verbosity: OutputInterface::VERBOSITY_NORMAL
            ));
        } catch (\Throwable $throwable) {
            $this->components->error($throwable->getMessage());
        }

        $this->components->success('All optimized.');
    }
}
